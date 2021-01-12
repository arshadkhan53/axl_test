<?php

namespace Drupal\axl_test\Plugin\rest\resource;

use Drupal\Core\Config\Config;
use Drupal\node\Entity\Node;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Provides a page node Resource.
 *
 * @RestResource(
 *   id = "page_node_resource",
 *   label = @Translation("Page Node Resource"),
 *   uri_paths = {
 *     "canonical" = "/page/{site_api_key}/{nid}"
 *   }
 * )
 */
class PageNodeResource extends ResourceBase {

  /**
   * The available serialization formats.
   *
   * @var array
   */
  protected $serializerFormats = [];

  /**
   * A logger instance.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * A config instance.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * Constructs a Drupal\rest\Plugin\ResourceBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Config\Config $config
   *   A config instance.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger, Config $config) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->config = $config;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition, $container->getParameter('serializer.formats'), $container->get('logger.factory')
      ->get('rest'), $container->get('config.factory')->get('system.site'));
  }

  /**
   * Responds to entity GET requests.
   *
   * @param string $site_api_key
   *   The site api key..
   * @param string $nid
   *   The node id.
   *
   * @return \Drupal\rest\ResourceResponse
   *   A json response.
   */
  public function get($site_api_key, $nid) {
    $node = Node::load($nid);
    if ($node && $node->bundle() === 'page' && $this->config->get('siteapikey') === $site_api_key) {
      // Response will convert node object to json.
      return new ResourceResponse($node);
    }
    throw new AccessDeniedHttpException($this->t("Access denied"));
  }

}
