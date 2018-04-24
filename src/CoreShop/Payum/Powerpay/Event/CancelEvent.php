<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Payum\PowerpayBundle\Event;

use CoreShop\Component\Payment\Model\Payment;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use DachcomDigital\Payum\Powerpay\Request\Api\Cancel;
use Payum\Core\Payum;

class CancelEvent
{
    /**
     * @var Payum
     */
    protected $payum;

    /**
     * @var RepositoryInterface
     */
    protected $paymentRepository;

    /**
     * ConfirmEvent constructor.
     *
     * @param Payum               $payum
     * @param RepositoryInterface $paymentRepository
     */
    public function __construct(Payum $payum, RepositoryInterface $paymentRepository)
    {
        $this->payum = $payum;
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * @param PaymentInterface $payment
     * @throws \Payum\Core\Reply\ReplyInterface
     */
    public function cancel(PaymentInterface $payment)
    {
        if ($payment->getState() !== Payment::STATE_CANCELLED) {
            return;
        }

        $factoryName = $payment->getPaymentProvider()->getGatewayConfig()->getFactoryName();
        if ($factoryName !== 'powerpay') {
            return;
        }

        $saferpay = $this->payum->getGateway('powerpay');
        $saferpay->execute(new Cancel($payment));

    }
}