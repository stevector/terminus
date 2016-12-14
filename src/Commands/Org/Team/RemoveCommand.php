<?php

namespace Pantheon\Terminus\Commands\Org\Team;

use Pantheon\Terminus\Commands\TerminusCommand;

/**
 * Class RemoveCommand
 * @package Pantheon\Terminus\Commands\Org\Team
 */
class RemoveCommand extends TerminusCommand
{
    /**
     * Removes a user from an organization.
     *
     * @authorize
     *
     * @command org:team:remove
     *
     * @param string $organization Organization name or ID
     * @param string $member User UUID, email address, or full name
     *
     * @usage terminus org:team:remove <organization> <user>
     *     Removes the user, <user>, from <organization>.
     */
    public function remove($organization, $member)
    {
        $org = $this->session()->getUser()->getOrgMemberships()->get($organization)->getOrganization();
        $membership = $org->getUserMemberships()->fetch()->get($member);
        $workflow = $membership->delete();
        while (!$workflow->checkProgress()) {
            // @TODO: Remove Symfony progress bar to indicate that something is happening.
        }
        $this->log()->notice(
            '{member} has been removed from the {org} organization.',
            ['member' => $membership->getUser()->get('profile')->full_name, 'org' => $org->get('profile')->name,]
        );
    }
}
