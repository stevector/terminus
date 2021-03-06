<?php

namespace Pantheon\Terminus\Collections;

use Pantheon\Terminus\Models\OrganizationSiteMembership;
use Pantheon\Terminus\Models\SiteOrganizationMembership;
use Pantheon\Terminus\Models\Workflow;

/**
 * Class SiteOrganizationMemberships
 * @package Pantheon\Terminus\Collections
 */
class SiteOrganizationMemberships extends SiteOwnedCollection
{
    /**
     * @var string
     */
    protected $collected_class = 'Pantheon\Terminus\Models\SiteOrganizationMembership';

    /**
     * @var string
     */
    protected $url = 'sites/{site_id}/memberships/organizations';

    /**
     * @var boolean
     */
    protected $paged = true;

    /**
     * Adds this org as a member to the site
     *
     * @param string $name Name of site to add org to
     * @param string $role Role for supporting organization to take
     * @return Workflow
     **/
    public function create($name, $role)
    {
        $workflow = $this->getSite()->getWorkflows()->create(
            'add_site_organization_membership',
            ['params' => ['organization_name' => $name, 'role' => $role,],]
        );
        return $workflow;
    }

    /**
     * Returns UUID of organization with given name
     *
     * @param string $name A name to search for
     * @return SiteOrganizationMembership|null
     */
    public function findByName($name)
    {
        foreach ($this->models as $org_member) {
            $org = $org_member->getName();
            if ($name == $org) {
                return $org_member;
            }
        }
        return null;
    }

    /**
     * Returns UUID of organization with given name
     *
     * @param string $name A name to search for
     * @return SiteOrganizationMembership|null
     */
    public function getUUID($name)
    {
        foreach ($this->models as $org_member) {
            $org = $org_member->getName();
            if ($name == $org) {
                return $org_member;
            }
        }
        return null;
    }

    /**
     * Retrieves the model with organization of the given UUID or name
     *
     * @param string $id UUID or name of desired site membership instance
     * @return OrganizationSiteMembership
     */
    public function get($id)
    {
        $models = $this->getMembers();
        if (isset($models[$id])) {
            return $models[$id];
        } else {
            foreach ($models as $key => $membership) {
                if (in_array($id, [$membership->getOrganization()->id, $membership->getOrganization()->getName()])) {
                    return $membership;
                }
            }
        }
        return null;
    }
}
