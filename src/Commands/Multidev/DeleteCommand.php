<?php

namespace Pantheon\Terminus\Commands\Multidev;

use Pantheon\Terminus\Commands\TerminusCommand;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Pantheon\Terminus\Site\SiteAwareTrait;
use Pantheon\Terminus\Exceptions\TerminusException;

/**
 * Class DeleteCommand
 * @package Pantheon\Terminus\Commands\Multidev
 */
class DeleteCommand extends TerminusCommand implements SiteAwareInterface
{
    use SiteAwareTrait;

    /**
     * Deletes a Multidev environment.
     *
     * @authorize
     *
     * @command multidev:delete
     * @aliases env:delete
     *
     * @param string $site_env site & Multidev environment in the format `site-name.env`
     * @option boolean $delete-branch Delete associated Git branch
     *
     * @usage terminus multidev:delete <site>.<multidev>
     *     Deletes <site>'s <multidev> Multidev environment.
     * @usage terminus multidev:delete awesome-site.multidev-env --delete-branch
     *     Deletes <site>'s <multidev> Multidev environment and deletes its associated Git branch.
     */
    public function deleteMultidev($site_env, $options = ['delete-branch' => false,])
    {
        list(, $env) = $this->getSiteEnv($site_env);
        $workflow = $env->delete(['delete_branch' => $options['delete-branch'],]);
        $workflow->wait();
        if ($workflow->isSuccessful()) {
            $this->log()->notice('Deleted the multidev environment {env}.', ['env' => $env->id,]);
        } else {
            throw new TerminusException($workflow->getMessage());
        }
    }
}
