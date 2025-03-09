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
if (!defined('_PS_VERSION_')) {
    exit;
}

class Itp_TecdocFunctionsModuleFrontController extends TecDocFrontController
{
    /** @var bool */
    public $ajax;

    /**
     * @return void
     */
    public function display(): void
    {
        $this->ajax = true;
    }

    /**
     * @return void
     *
     * @throws PrestaShopException
     */
    public function displayAjaxMakeAvailabilityRequest(): void
    {
        header('Content-Type: application/json');

        $product = (string) Tools::getValue('product');
        $quantity = (int) Tools::getValue('qty');
        $email = (string) Tools::getValue('email');
        $comment = (string) Tools::getValue('comment');
        $token = (string) Tools::getValue('token');

        if (!$product) {
            $this->errors['product'] = $this->trans('Product is required', [], 'Modules.Itptecdoc.Shop');
        }

        if (!$quantity > 0) {
            $this->errors['qty'] = $this->trans('Quantity is required', [], 'Modules.Itptecdoc.Shop');
        }

        if (!$email) {
            $this->errors['email'] = $this->trans('Email is required', [], 'Modules.Itptecdoc.Shop');
        }

        $message = '';

        if (!$this->errors) {
            $sendAvailabilityRequest = $this
                ->tecdoc
                ->forms()
                ->sendAvailabilityRequest($product, $quantity, $email, $comment, $token);

            if ($sendAvailabilityRequest) {
                $message = $this->trans('Your request has been successfully sent!', [], 'Modules.Itptecdoc.Shop');
            } else {
                $this->errors['product'] = $this->trans('An error occurred while sending the availability request!', [], 'Modules.Itptecdoc.Shop');
            }
        }

        $this->ajaxRender(json_encode([
            'message' => $message,
            'errors' => $this->errors,
        ]));
    }
}
