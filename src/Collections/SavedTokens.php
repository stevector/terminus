<?php

namespace Pantheon\Terminus\Collections;

use Pantheon\Terminus\DataStore\DataStoreAwareInterface;
use Pantheon\Terminus\DataStore\DataStoreAwareTrait;
use Pantheon\Terminus\Models\SavedToken;
use Pantheon\Terminus\Session\Session;
use Robo\Common\ConfigAwareTrait;
use Robo\Contract\ConfigAwareInterface;
use Pantheon\Terminus\Exceptions\TerminusException;

/**
 * Class SavedTokens
 * @package Pantheon\Terminus\Collections
 */
class SavedTokens extends TerminusCollection implements ConfigAwareInterface, DataStoreAwareInterface
{
    use ConfigAwareTrait;
    use DataStoreAwareTrait;

    /**
     * @var Session
     */
    public $session;
    /**
     * @var string
     */
    protected $collected_class = 'Pantheon\Terminus\Models\SavedToken';

    /**
     * @inheritdoc
     */
    public function __construct($options = [])
    {
        parent::__construct($options);
        $this->session = $options['session'];
    }

    /**
     * Saves a machine token to the tokens directory and logs the user in
     *
     * @param string $token The machine token to be saved
     */
    public function create($token_string)
    {
        $token =  $this->getContainer()->get(
            SavedToken::class,
            [(object)['token' => $token_string,], ['collection' => $this,]]
        );
        $token->setDataStore($this->getDataStore());
        $user = $token->logIn();
        $user->fetch();
        $token->id = $user->get('email');
        $token->set('email', $user->get('email'));
        $token->saveToDir();
        $this->models[$token->id] = $token;
    }

    /**
     * Adds a model to this collection
     *
     * @param object $model_data Data to feed into attributes of new model
     * @param array $options Data to make properties of the new model
     * @return TerminusModel
     */
    public function add($model_data, array $options = [])
    {
        $model = parent::add($model_data, $options);
        $model->setDataStore($this->getDataStore());
        return $model;
    }


    /**
     * Retrieves the model with site of the given email or machine token
     *
     * @param string $id Email or machine token to look up a saved token by
     * @return \Pantheon\Terminus\Models\SavedToken
     * @throws \Pantheon\Terminus\Exceptions\TerminusException
     */
    public function get($id)
    {
        $tokens = $this->getMembers();
        if (isset($tokens[$id])) {
            return $tokens[$id];
        } else {
            foreach ($tokens as $token) {
                if ($id == $token->get('token')) {
                    return $token;
                }
            }
        }
        throw new TerminusException('Could not find a saved token identified by {id}.', compact('id'));
    }

    /**
     * Delete all of the saved tokens.
     */
    public function deleteAll()
    {
        foreach ($this->getMembers() as $token) {
            $token->delete();
        }
    }
    
    /**
     * @inheritdoc
     */
    protected function getCollectionData($options = [])
    {
        $tokens = [];
        foreach ($this->getDataStore()->keys() as $key) {
            $tokens[] = $this->getDataStore()->get($key);
        }
        return $tokens;
    }
}
