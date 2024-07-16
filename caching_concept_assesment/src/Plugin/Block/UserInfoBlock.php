<?php

namespace Drupal\caching_concept_assesment\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block of user info.
 *
 * @Block(
 *   id = "custom_module_userinfo_block",
 *   admin_label = @Translation("User Info Block"),
 * )
 */
class UserInfoBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccountInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
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
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   *
   * Build the Call For latest article list.
   */
  public function build() {
    $user_email = $this->currentUser->getEmail();
    // Rendar markup.
    return [
      '#markup' => $this->t('Current user email: @user_email', ['@user_email' => $user_email]),
      '#cache' => [
        'contexts' => ['user'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockAccess(AccountInterface $account) {
    return $account->isAuthenticated() ? \Drupal\Core\Access\AccessResult::allowed() : \Drupal\Core\Access\AccessResult::forbidden();
  }

}