<?php

/**
 * @file
 * Contains \Drupal\rules\Plugin\Condition\UserIsBlocked.
 */

namespace Drupal\rules\Plugin\Condition;

use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Path has URL alias' condition.
 *
 * @Condition(
 *   id = "rules_path_has_url_alias",
 *   label = @Translation("Path has URL alias"),
 *   context = {
 *     "path" = {
 *       "label" = "The path",
 *       "type" = "string",
 *       "required" = "TRUE"
 *     },
 *     "language" = {
 *      "label" = "The language",
 *      "type" = "string",
 *      "required" = "TRUE"
 *    }
 *   }
 * )
 *
 * @todo: Add access callback information from Drupal 7.
 * @todo: Add group information from Drupal 7.
 */
class UrlAlias extends ConditionPluginBase implements ContainerFactoryPluginInterface {

  protected $aliasManager;

  /**
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   * @param AliasManagerCacheDecorator $alias_manager AliasManager Service
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AliasManagerCacheDecorator $alias_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->aliasManager = $alias_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition,
      $container->get('path.alias_manager.cached')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function summary() {
    return t('Path has URL alias');
  }

  /**
   * {@inheritdoc}
   */
  public function evaluate() {

    $path = $this->getContextValue('path');
    $language = $this->getContextValue('language');
    return (bool) $this->aliasManager->getAliasByPath($path, $language);
  }

}
