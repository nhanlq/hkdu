<?php

namespace Drupal\cme_user\Commands;

use Drush\Commands\DrushCommands;

/**
 * Provide Drush commands for all the Devel Generate processes.
 *
 * For commands that are parts of modules, Drush expects to find commandfiles in
 * __MODULE__/src/Commands, and the namespace is Drupal/__MODULE__/Commands.
 *
 * In addition to a commandfile like this one, you need to add a
 * drush.services.yml in the root of your module like this module does.
 *
 * Note: Integer values for defaults need to be in quotes, otherwise they can
 * match with numeric constants such as InputOption::VALUE_OPTIONAL in
 * Consolidation\AnnotatedCommand\Parser\CommandInfo::createInputOptions()
 * and consequently get removed from the help output.
 */
class UpdateUserCommands extends DrushCommands {

  /**
   * Drush command that displays the given text.
   *
   * @command cme_user:update_user
   * @aliases update-user upusers
   * @usage cme_user:update_user
   */
  public function update_user() {
    $message = $this->getCommand();
    $this->output()->writeln($message);
  }

  /**
   * @return mixed
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function getCommand() {
    $users = [];
    $ids = \Drupal::entityQuery('user')->condition('status', 1)->execute();
    $result = \Drupal\user\Entity\User::loadMultiple($ids);
    foreach ($result as $user) {
      if (in_array('hkdu_administrator', $user->getRoles())) {
        $users[] = $user->get('field_mchk_license')->value;
        $user->set('field_hkdu_administrator', 1);
      }else{
        $user->set('field_hkdu_administrator', 0);
      }
      $user->save();
    }
    if ($users) {
      $message = 'The Member : ' . implode(",", $users).' were updated.';
    }
    else {

      $message = 'No Member was updated.';
    }

    return $message;

  }
}
