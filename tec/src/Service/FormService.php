<?php

/**
 * 2014-2025 IT PREMIUM OU
 *
 * NOTICE OF LICENSE
 *
 * This module is licensed for use on one single domain. To use this module on additional domains,
 * you must purchase additional licenses. Redistribution, resale, leasing, licensing, or offering
 * this resource to third parties is prohibited.
 *
 * The data used in this module, especially the complete database, may not be copied.
 * It is strictly prohibited to duplicate the data and database and distribute the same,
 * and/or instruct third parties to engage in such activities, without prior consent from TecAlliance.
 * Any use of content in a manner not expressly authorized constitutes copyright infringement and violators will be prosecuted.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author IT PREMIUM OU <info@itpremium.net>
 * @copyright  2014-2025 IT PREMIUM OU.
 * @license Single Domain License
 *
 * PrestaShop and TecDoc are International Registered Trademarks, respective properties of PrestaShop SA and TecAlliance GmbH.
 * IT PREMIUM OU is not associated with TecAlliance GmbH or PrestaShop SA and their services, all rights belong to their respective owners.
 */

declare(strict_types=1);

namespace ItPremium\TecDoc\Service;

use ItPremium\TecDoc\Constant\ConfigurationConstant;
use ReCaptcha\ReCaptcha;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class FormService
{
    /**
     * @param string $product
     * @param int $quantity
     * @param string $email
     * @param string $comment
     * @param string $recaptchaResponse
     *
     * @return int|bool
     */
    public function sendAvailabilityRequest(string $product, int $quantity, string $email, string $comment, string $recaptchaResponse): int|bool
    {
        $recaptchaEnabled = \Configuration::get(ConfigurationConstant::TECDOC_RECAPTCHA_ENABLE);

        if ($recaptchaEnabled and !$this->verifyRecaptcha($recaptchaResponse)) {
            return false;
        }

        $notificationEmail = \Configuration::get(ConfigurationConstant::TECDOC_EMAIL_FOR_AVAILABILITY_REQUESTS);

        if (!$notificationEmail or !\Validate::isEmail($notificationEmail)) {
            $notificationEmail = \Configuration::get('PS_SHOP_EMAIL');
        }

        $context = \Context::getContext();

        return \Mail::Send(
            $context->language->id,
            'availability_request',
            $context->getTranslator()->trans('Availability request', [], 'Modules.Itptecdoc.Shop'),
            [
                '{product}' => $product,
                '{quantity}' => $quantity,
                '{email}' => $email,
                '{comment}' => $comment,
            ],
            $notificationEmail,
            null,
            null,
            null,
            null,
            null,
            _PS_MODULE_DIR_ . 'itp_tecdoc/mails',
            false,
            null,
            null,
            $email
        );
    }

    /**
     * @param string $recaptchaResponse
     *
     * @return bool
     */
    private function verifyRecaptcha(string $recaptchaResponse): bool
    {
        $secretKey = \Configuration::get(ConfigurationConstant::TECDOC_RECAPTCHA_SECRET_KEY);

        if (!$secretKey) {
            return false;
        }

        $reCaptcha = new ReCaptcha($secretKey);

        $response = $reCaptcha->verify($recaptchaResponse);

        return $response->isSuccess();
    }
}
