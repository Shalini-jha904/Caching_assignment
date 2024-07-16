<?php

namespace Drupal\caching_concept_assesment\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block of article list.
 *
 * @Block(
 *   id = "custom_module_articlelist_block",
 *   admin_label = @Translation("Latest Article list Block"),
 * )
 */
class LatestArticlelistBLock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   *
   * Build the Call For latest article list.
   */
  public function build() {
    $nids = $this->getArticleIds();
    $nodes = Node::loadMultiple($nids);
    $titles = [];
    foreach ($nodes as $node) {
      $titles[] = [
        '#type' => 'link',
        '#title' => $node->getTitle() . rand(),
        '#url' => $node->toUrl(),
      ];
    }
    // Rendar article item list.
    return [
      '#theme' => 'item_list',
      '#title' => $this->t('Recent articles'),
      '#list_type' => 'ul',
      '#items' => $titles,
    ];
  }

  /**
   * Get the node IDs.
   *
   * @return array
   *   An array of node IDs.
   */
  protected function getArticleIds() {
    $query = $this->entityTypeManager->getStorage('node')
      ->getQuery()
      ->condition('type', 'article')
      ->condition('status', 1)
      ->sort('created', 'DESC')
      ->accessCheck(FALSE)
      ->range(0, 3);
    return $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    $tags = [];
    $nids = $this->getArticleIds();

    if (!empty($nids)) {
      foreach ($nids as $nid) {
        $tags[] = 'node:' . $nid;
      }
    }
    return Cache::mergeTags(parent::getCacheTags(), $tags);
  }

}