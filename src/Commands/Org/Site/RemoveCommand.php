<?php

namespace Pantheon\Terminus\Commands\Org\Site;

use Pantheon\Terminus\Commands\TerminusCommand;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Pantheon\Terminus\Site\SiteAwareTrait;

/**
 * Class RemoveCommand
 * @package Pantheon\Terminus\Commands\Org\Site
 */
class RemoveCommand extends TerminusCommand implements SiteAwareInterface
{
    use SiteAwareTrait;

    /**
     * Removes a site from an organization.
     *
     * @authorize
     *
     * @command org:site:remove
     * @aliases org:site:rm
     *
     * @param string $organization Organization name or ID
     * @param string $site Site name
     *
     * @usage terminus org:site:remove <organization> <site>
     *     Removes <site> from the <organization> organization.
     */
    public function remove($organization, $site)
    {
        $org = $this->session()->getUser()->getOrgMemberships()->get($organization)->getOrganization();
        $membership = $org->getSiteMemberships()->get($site);
        $workflow = $membership->delete();
        while (!$workflow->checkProgress()) {
            // @TODO: Remove Symfony progress bar to indicate that something is happening.
        }
        $this->log()->notice(
            '{site} has been removed from the {org} organization.',
            ['site' => $membership->site->get('name'), 'org' => $org->get('profile')->name,]
        );
    }
}
