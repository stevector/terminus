<?php

namespace Pantheon\Terminus\Collections;

use Pantheon\Terminus\Models\Organization;
use Pantheon\Terminus\Exceptions\TerminusException;
use Pantheon\Terminus\Exceptions\TerminusNotFoundException;

/**
 * Class OrganizationSiteMemberships
 * @package Pantheon\Terminus\Collections
 */
class OrganizationSiteMemberships extends TerminusCollection
{
    /**
     * @var Organization
     */
    public $organization;
    /**
     * @var string
     */
    protected $collected_class = 'Pantheon\Terminus\Models\OrganizationSiteMembership';
    /**
     * @var boolean
     */
    protected $paged = true;

    /**
     * Instantiates the collection
     *
     * @param array $options To be set
     * @return OrganizationSiteMemberships
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);
        $this->organization = $options['organization'];
        $this->url = "organizations/{$this->organization->id}/memberships/sites";
    }

    /**
     * Adds a site to this organization
     *
     * @param Site $site Site object of site to add to this organization
     * @return Workflow
     */
    public function create($site)
    {
        $workflow = $this->getOrganization()->getWorkflows()->create(
            'add_organization_site_membership',
            ['params' => ['site_id' => $site->id, 'role' => 'team_member',],]
        );
        return $workflow;
    }

    /**
     * Retrieves the model with site of the given UUID or name
     *
     * @param string $id UUID or name of desired site membership instance
     * @return OrganizationSiteMembership
     * @throws TerminusNotFoundException
     */
    public function get($id)
    {
        $models = $this->getMembers();
        if (isset($models[$id])) {
            return $models[$id];
        }
        foreach ($models as $key => $membership) {
            if (in_array($id, [$membership->getSite()->id, $membership->getSite()->get('name')])) {
                return $membership;
            }
        }
        throw new TerminusNotFoundException(
            'A site identified by {id} could not be found belonging to this organization.',
            compact('id')
        );
    }

    /**
     * @return Organization
     */
    public function getOrganization()
    {
        if (empty($this->organization)) {
            $this->organization = $this->getContainer()->get(Organization::class, [$this->get('organization')]);
        }
        return $this->organization;
    }

    /**
     * Retrieves the matching site from model members
     *
     * @param string $site_id ID or name of desired site
     * @return Site $membership->site
     * @throws TerminusException
     */
    public function getSite($site_id)
    {
        if (is_null($membership = $this->get($site_id))) {
            throw new TerminusException(
                'This user is not a member of an organization identified by {id}.',
                ['id' => $site_id,]
            );
        }
        return $membership->getSite();
    }

    /**
     * Determines whether a site is a member of this collection
     *
     * @param Site $site Site to determine membership of
     * @return bool
     */
    public function siteIsMember($site)
    {
        $is_member = !is_null($this->get($site));
        return $is_member;
    }
}
