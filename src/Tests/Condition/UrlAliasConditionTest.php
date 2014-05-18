<?php

/**
 * @file
 * Contains Drupal\rules\Tests\Condition\UrlAliasConditionTest.
 */

namespace Drupal\rules\Tests\Condition;

use Drupal\system\Tests\Entity\EntityUnitTestBase;
use Drupal\Core\Path\AliasStorage;
use Drupal\Core\Database\Database;
use Drupal\Core\Path\AliasManager;
use Drupal\system\Tests\Path\PathUnitTestBase;

/**
 * Tests the node conditions.
 */
class UrlAliasConditionTest extends PathUnitTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = array('system', 'rules', 'path');
  protected $alias_manager;

  public static function getInfo() {
    return array(
      'name' => 'UrlAlias Condition Plugin',
      'description' => 'Tests that conditions, provided by the the UrlAlias module, are working properly.',
      'group' => 'Rules',
    );
  }

  public function setUp() {
    parent::setUp();
    // path.alias_manager.cached
    $connection = Database::getConnection();
    $this->fixtures->createTables($connection);
    $this->alias_manager = $this->container->get('path.alias_manager.cached');

    //Create Path object.
    $aliasStorage = new AliasStorage($connection, $this->container->get('module_handler'));

    //@todo: replace language 'und' with Language::LANGCODE_NOT_SPECIFIED
    $aliasStorage->save('original', 'alias', 'und');
    \Drupal::service('path.alias_manager.cached')->cacheClear();

  }

  /**
   * Tests conditions.
   */
  function testConditions() {

    $manager = $this->container->get('plugin.manager.condition', $this->container->get('container.namespaces'));

    $condition = $manager->createInstance('rules_path_has_url_alias')
      ->setContextValue('path', 'notexisting')
      ->setContextValue('language', 'und');

    $this->assertFalse($condition->execute(), 'Path has no alias.');

    $condition->setContextValue('path', 'original');
    $this->assertTrue($condition->execute(), 'Path has an alias.');

  }
}
