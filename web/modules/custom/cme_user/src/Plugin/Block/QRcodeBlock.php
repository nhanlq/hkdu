<?php

namespace Drupal\cme_user\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'QRcodeBlock' block.
 *
 * @Block(
 *  id = "qrcode_block",
 *  admin_label = @Translation("Qrcode block"),
 * )
 */
class QRcodeBlock extends BlockBase
{

    /**
     * {@inheritdoc}
     */
    public function build()
    {

        $google_qr_current_url = \Drupal::request()->getUri();
        $current_path = \Drupal::service('path.current')->getPath();
        $path = explode('/', $current_path);
        $id = $path[2];
        $user = \Drupal\user\Entity\User::load($id);
        $render_type = \Drupal::config('google_qr_code.settings')->get('whenshow');
        $host = \Drupal::request()->getSchemeAndHttpHost();
       // $google_qr_current_url = $host . '/user/' . $uid;

        $qr_code_height = \Drupal::config('google_qr_code.settings')->get('height');
        $qr_code_width = \Drupal::config('google_qr_code.settings')->get('width');
        // Get the google charts API image URL.
        $google_qr_image_url = "https://chart.googleapis.com/chart?chs=" .
            $qr_code_width . "x" . $qr_code_height
            . "&cht=qr&chl=" . $google_qr_current_url . '&chld=H|0';

        // Write the alternate description for the QR image.
        $google_qr_alt = $this->t('QR Code for @url', array('@url' => $google_qr_current_url));

        // Return markup, and return the block as being cached per URL path.
        $code = array(
            '#theme' => 'image',
            '#uri' => $google_qr_image_url,
            '#width' => $qr_code_width,
            '#height' => $qr_code_height,
            '#alt' => $google_qr_alt,
            '#class' => 'google-qr-code-image',
            '#cache' => array(
                'contexts' => array('url.path'),
            ),
        );
        return [
            '#theme' => 'cme_user_code',
            '#user' => $user,
            '#qrcode' => $code,
            '#cache' => [
                'max-age' => 0,
            ],
        ];
    }

}
