<?php

namespace Drupal\cme_event\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
/**
 * Class PaymentController.
 */
class PaymentController extends ControllerBase
{

    /**
     * Payment.
     *
     * @return string
     *   Return Hello string.
     */
    public function payment($eventId, $uid, $productId)
    {
        $password = 'User123@';
        $users = \Drupal::entityTypeManager()
            ->getStorage('user')
            ->loadByProperties([
                'uid' => $uid,
            ]);

        $user = reset($users);
// Set the new password
        $user->setPassword($password);
// Save the user
        $user->save();

        //Auto login
        $username = $user->getUserName();
        if (\Drupal::service('user.auth')->authenticate($username, $password)) {
            user_login_finalize($user);
            $response = new RedirectResponse('/e-pharm/addcart/'.$productId);
            $response->send();
        }
    }

}
