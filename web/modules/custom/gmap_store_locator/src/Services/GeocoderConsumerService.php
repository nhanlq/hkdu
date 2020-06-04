<?php

namespace Drupal\store_locator\Services;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use GuzzleHttp\ClientInterface;
use Drupal\Component\Serialization\Json;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the GeocoderConsumerService service, for return parse GeoJson.
 */
class GeocoderConsumerService {

  /**
   * Drupal http client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * Language Manager service.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Config Factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  private $configFactory;

  /**
   * Service constructor.
   *
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The http client.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(
      ClientInterface $http_client,
      LanguageManagerInterface $language_manager,
      ConfigFactoryInterface $config_factory
  ) {
    $this->httpClient = $http_client;
    $this->languageManager = $language_manager;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client'),
      $container->get('language_manager'),
      $container->get('config.factory')
    );
  }

  /**
   * Return json list of geolocation matching $text.
   *
   * @param string $address
   *   The address query for search a place.
   *
   * @return array
   *   An array of matching location.
   */
  public function geoLatLong($address) {
    $language_interface = $this->languageManager->getCurrentLanguage();
    $language = isset($language_interface) ? $language_interface->getId() : 'en';
    $config = $this->configFactory->getEditable('store_locator.settings');
    $geocodes = ['latitude' => '', 'longitude' => ''];

    $query = [
      'key' => $config->get('api_key'),
      'address' => $address,
      'language' => $language,
      'sensor' => 'false',
    ];
    $uri = 'https://maps.googleapis.com/maps/api/geocode/json';

    $response = $this->httpClient->request('GET', $uri, [
      'query' => $query,
    ]);

    if (empty($response->error)) {
      $data = Json::decode($response->getBody());

      if (strtoupper($data['status']) == 'OK') {
        $lat = $data['results'][0]['geometry']['location']['lat'];
        $lng = $data['results'][0]['geometry']['location']['lng'];
        $geocodes = ['latitude' => $lat, 'longitude' => $lng];
      }
    }
    return $geocodes;
  }

}
