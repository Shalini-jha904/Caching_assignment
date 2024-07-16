<?php

namespace Drupal\caching_concept_assesment\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;


/**
 * Provides a block of article list.
 *
 * @Block(
 *   id = "custom_module_articlelistbytag_block",
 *   admin_label = @Translation("Article list by tag Block"),
 * )
 */
class ArticlelistbytagBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   *
   * Build the Call For latest article list.
   */
  public function build() {
    $user = $this->entityTypeManager->getStorage('user')->load($this->currentUser->id());
    $user_tag = $user->get('field_article_category')->target_id;
    $nids = $this->getArticleIds($user_tag);
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
      '#title' => $this->t('Releted articles'),
      '#list_type' => 'ul',
      '#items' => $titles,
      '#cache' => [
        'contexts' => ['user'],
        'tags' => ['node_list: article', 'user:' . $this->currentUser->id()],
      ],
    ];
  }

  /**
   * Get the articles based on selected Tag.
   *
   * @return array
   *   An array of node IDs.
   */
  protected function getArticleIds($user_tag) {
    $query = $this->entityTypeManager->getStorage('node')
      ->getQuery()
      ->condition('type', 'article')
      ->condition('status', 1)
      ->condition('field_tags', $user_tag)
      ->sort('created', 'DESC')
      ->accessCheck(FALSE);
    return $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function blockAccess(AccountInterface $account) {
    return $account->isAuthenticated() ? AccessResult::allowed() : AccessResult::forbidden();
  }


}