<?php

namespace Drupal\global_hkdu\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'LoginBlock' block.
 *
 * @Block(
 *  id = "login_block",
 *  admin_label = @Translation("Login block"),
 * )
 */
class LoginBlock extends BlockBase
{

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $islogin = false;
        if (\Drupal::currentUser()->isAuthenticated()) {
            $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
            $islogin = true;
        }

        return [
            '#theme' => ['login_block'],
            '#user' => $user,
            '#islogin' => $islogin,
            '#cache' => [
                'max-age' => 0,
            ],
        ];
    }

}
