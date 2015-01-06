<?php

/*
 * This file is part of the vSymfo package.
 *
 * website: www.vision-web.pl
 * (c) Rafał Mikołajun <rafal@vision-web.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vSymfo\Payment\EgoPayBundle\Controller;

use JMS\Payment\CoreBundle\Entity\PaymentInstruction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * EgoPay - dane zwrotne
 * @author Rafał Mikołajun <rafal@vision-web.pl>
 * @package vSymfoPaymentEgopayBundle
 */
class CallbackController extends Controller
{
    /**
     * @param Request $request
     * @param PaymentInstruction $instruction
     * @return Response
     * @throws \EgoPayException
     */
    public function callbackAction(Request $request, PaymentInstruction $instruction)
    {
        $client = $this->get('payment.egopay.client');
        $aResponse = $client->createSciCallback()->getResponse($request->request->all());

        if (null === $transaction = $instruction->getPendingTransaction()) {
            return new Response('No pending transaction found for the payment instruction', 500);
        }

        $em = $this->getDoctrine()->getManager();
        $transaction->getExtendedData()->set('sId', $aResponse['sId']);
        $transaction->getExtendedData()->set('sDate', isset($aResponse['sDate']) ? $aResponse['sDate'] : '');
        $transaction->getExtendedData()->set('fAmount', $aResponse['fAmount']);
        $transaction->getExtendedData()->set('sCurrency', $aResponse['sCurrency']);
        $transaction->getExtendedData()->set('fFee', isset($aResponse['fFee']) ? $aResponse['fFee'] : '');
        $transaction->getExtendedData()->set('sType', $aResponse['sType']);
        $transaction->getExtendedData()->set('iTypeId', $aResponse['iTypeId']);
        $transaction->getExtendedData()->set('sEmail', isset($aResponse['sEmail']) ? $aResponse['sEmail'] : '');
        $transaction->getExtendedData()->set('sDetails', isset($aResponse['sDetails']) ? $aResponse['sDetails'] : '');
        $transaction->getExtendedData()->set('sStatus', $aResponse['sStatus']);
        $transaction->getExtendedData()->set('cf_1', isset($aResponse['cf_1']) ? $aResponse['cf_1'] : '');
        $transaction->getExtendedData()->set('cf_2', isset($aResponse['cf_2']) ? $aResponse['cf_2'] : '');
        $transaction->getExtendedData()->set('cf_3', isset($aResponse['cf_3']) ? $aResponse['cf_3'] : '');
        $transaction->getExtendedData()->set('cf_4', isset($aResponse['cf_4']) ? $aResponse['cf_4'] : '');
        $transaction->getExtendedData()->set('cf_5', isset($aResponse['cf_5']) ? $aResponse['cf_5'] : '');
        $transaction->getExtendedData()->set('cf_6', isset($aResponse['cf_6']) ? $aResponse['cf_6'] : '');
        $transaction->getExtendedData()->set('cf_7', isset($aResponse['cf_7']) ? $aResponse['cf_7'] : '');
        $transaction->getExtendedData()->set('cf_8', isset($aResponse['cf_8']) ? $aResponse['cf_8'] : '');
        $em->persist($transaction);

        $payment = $transaction->getPayment();
        $result = $this->get('payment.plugin_controller')->approveAndDeposit($payment->getId(), (float)$aResponse['fAmount']);
        if (is_object($ex = $result->getPluginException())) {
            throw $ex;
        }

        $em->flush();

        return new Response('OK');
    }
}
