<?php

/**
 * ShippingService.php
 *
 * @copyright  2022 beikeshop.com - All Rights Reserved
 * @link       https://beikeshop.com
 * @author     Edward Yang <yangjin@guangda.work>
 * @created    2022-07-22 17:58:14
 * @modified   2022-07-22 17:58:14
 */

namespace Beike\Shop\Services\TotalServices;

use Beike\Shop\Services\TotalService;
use Illuminate\Support\Str;

class ShippingService
{
    /**
     * @param TotalService $totalService
     * @return array|null
     * @throws \Exception
     */
    public static function getTotal(TotalService $totalService): ?array
    {
        $shippingMethod = $totalService->shippingMethod;
        if (empty($shippingMethod)) {
            return null;
        }

        $methodArray = explode('.', $shippingMethod);
        $pluginCode = Str::studly($methodArray[0]);
        $className = "Plugin\\{$pluginCode}\\Bootstrap";

        if (!method_exists($className, 'getShippingFee')) {
            throw new \Exception("请在插件 {$className} 实现方法: public function getShippingFee(\$totalService)");
        }
        $amount = (float)(new $className)->getShippingFee($totalService);
        $totalData = [
            'code' => 'shipping',
            'title' => trans('shop/carts.shipping_fee'),
            'amount' => $amount,
            'amount_format' => currency_format($amount)
        ];

        $totalService->amount += $totalData['amount'];
        $totalService->totals[] = $totalData;

        return $totalData;
    }
}
