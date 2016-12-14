<?php

namespace Pantheon\Terminus\Commands\PaymentMethod;

use Pantheon\Terminus\Commands\TerminusCommand;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Pantheon\Terminus\Site\SiteAwareTrait;

/**
 * Class AddCommand
 * @package Pantheon\Terminus\Commands\PaymentMethod
 */
class AddCommand extends TerminusCommand implements SiteAwareInterface
{
    use SiteAwareTrait;

    /**
     * Associates an existing payment instrument with a site.
     *
     * @authorize
     *
     * @command payment-method:add
     * @aliases pm:add
     *
     * @param string $site_name Site name
     * @param string $payment_method Payment instrument label or UUID
     *
     * @usage terminus payment-method:add <site> <instrument>
     *     Associates <instrument> payment instrument with <site>.
     */
    public function add($site_name, $payment_method)
    {
        $site = $this->getSite($site_name);
        $pm = $this->session()->getUser()->getPaymentMethods()->fetch()->get($payment_method);
        $site->addPaymentMethod($pm->id)->wait();
        $this->log()->notice(
            '{method} has been applied to the {site} site.',
            ['method' => $pm->get('label'), 'site' => $site->get('name'),]
        );
    }
}
