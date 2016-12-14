<?php

namespace Pantheon\Terminus\Commands\PaymentMethod;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Pantheon\Terminus\Commands\TerminusCommand;

/**
 * Class ListCommand
 * @package Pantheon\Terminus\Commands\PaymentMethod
 */
class ListCommand extends TerminusCommand
{
    /**
     * Displays the list of payment instruments for the currently logged-in user.
     *
     * @authorize
     *
     * @command payment-method:list
     * @aliases payment-methods pm:list pms
     *
     * @field-labels
     *     label: Label
     *     id: ID
     * @return RowsOfFields
     *
     * @usage terminus payment-method:list
     *     Displays the list of payment instruments for the currently logged-in user.
     */
    public function listPaymentMethods()
    {
        $methods = array_map(
            function ($method) {
                return $method->serialize();
            },
            $this->session()->getUser()->getPaymentMethods()->fetch()->all()
        );
        if (empty($methods)) {
            $this->log()->notice('There are no payment methods attached to this account.');
        }
        return new RowsOfFields($methods);
    }
}
