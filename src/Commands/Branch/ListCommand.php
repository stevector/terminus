<?php

namespace Pantheon\Terminus\Commands\Branch;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Pantheon\Terminus\Commands\TerminusCommand;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Pantheon\Terminus\Site\SiteAwareTrait;

/**
 * Class ListCommand
 * @package Pantheon\Terminus\Commands\Branch
 */
class ListCommand extends TerminusCommand implements SiteAwareInterface
{
    use SiteAwareTrait;

    /**
     * Displays list of git branches for a site.
     *
     * @authorize
     *
     * @command branch:list
     * @aliases branches
     *
     * @field-labels
     *     id: ID
     *     sha: SHA
     * @return RowsOfFields
     *
     * @param string $site_id Site name
     *
     * @usage terminus branch:list <site>
     *     Displays a list of Git branches within <site>'s Pantheon remote repository.
     */
    public function listBranches($site_id)
    {
        $site = $this->getSite($site_id);
        $branches = array_map(
            function ($branch) {
                return $branch->serialize();
            },
            $site->getBranches()->all()
        );
        return new RowsOfFields($branches);
    }
}
