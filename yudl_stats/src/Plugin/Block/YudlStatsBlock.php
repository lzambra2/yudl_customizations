<?php

namespace Drupal\yudl_stats\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Url;

 /**
 * Provides a 'YudlStatsBlock' block.
 *
 * @Block(
 *  id = "yudl_stats_block",
 *  admin_label = @Translation("Yudl stats block"),
 * )
 */

class YudlStatsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity repository service.
   *
   * @var \Drupal\Core\Entity\EntityRepositoryInterface
   */
  protected $entityRepository;

    /**
   * The entity repository service.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;

  /**
   * Constructs a new NodeTranslationLanguagesBlock object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager service.
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entityRepository
   *   The entity repository service.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, EntityRepositoryInterface $entityRepository, CurrentRouteMatch $currentRouteMatch,
  ) {
    $this->entityTypeManager = $entityTypeManager;
    $this->entityRepository = $entityRepository;
    $this->currentRouteMatch = $currentRouteMatch;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('entity.repository'),
      $container->get('current_route_match')

    );
  }

  /**
   * Makes the markup for a given item's box for the template.
   *
   * @param string $string
   *   The inside text string for the box that will be linked.
   * @param Drupal\Core\Url $link_url
   *   The URL object for the explore link.
   *
   * @return string
   *   Markup of the box for use in the template
   */
  private function makeBox($string, Url $link_url = NULL) {
    if ($link_url) {
      // Drupal's Link class is escaping the HTML, so this must be done
      // manually.
      return '<div class="stats_box col-4 stats_pointer_box"><a href="' . $link_url->toString() . '" title="Explore items">' .
        '<div class="stats_border_box">' . $string . '</div></a></div>';
    }
    else {
      return '<div class="stats_box col-4"><div class="stats_border_box">' . $string . '</div></div>';
    }
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $collection_node = $this->currentRouteMatch->getParameter('node');
    $max_timestamp = 0;
    $stat_box_row1 = $stat_box_row2 = [];
    $collection_created = $collection_node->get('revision_timestamp')->getString();
    $stat_box_row1[] = $this->makeBox("<strong>" . number_format(0) . "</strong><br>items");
    $stat_box_row1[] = $this->makeBox("<strong>" . "0". "</strong><br>resource types");
    $stat_box_row1[] = $this->makeBox("<strong>" . number_format(0) . "</strong><br>unique languages");
    $stat_box_row2[] = $this->makeBox("<strong>" . (($collection_created) ? date('Y', $collection_created) : 'unknown') .
      "</strong><br>collection created");
    $stat_box_row2[] = $this->makeBox("<strong>" . (($max_timestamp) ? date('M d, Y', $max_timestamp) : 'unknown') .
      "</strong><br>most recent item added</div>");
    return [
      '#markup' =>
      (count($stat_box_row1) > 0) ?
        // ROW 1.
      '<div class="container"><div class="row">' .
      implode('', $stat_box_row1) .
      '</div>' .
        // ROW 2.
      '<div class="row">' .
      implode('', $stat_box_row2) .
      '</div>' :
      "",
    ];    
  }
}
