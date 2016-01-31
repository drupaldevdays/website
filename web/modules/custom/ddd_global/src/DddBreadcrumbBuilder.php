<?php

/**
 * @file
 * Contains \Drupal\ddd_global\DddBreadcrumbBuilder.
 */

namespace Drupal\ddd_global;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Provides a breadcrumb builder for nodes in a book.
 */
class DddBreadcrumbBuilder implements BreadcrumbBuilderInterface {
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match) {
    return ($route_match->getRouteName() === 'entity.contact_form.canonical');
  }

  /**
   * {@inheritdoc}
   */
  public function build(RouteMatchInterface $route_match) {
    $breadcrumb = new Breadcrumb();

    $links = array();

    $contact_form = $route_match->getParameter('contact_form');
    $label = $contact_form->label();
    $id = $contact_form->id();

    $links[] = Link::createFromRoute($this->t('Home'), '<front>');

    if (in_array($id, ['volunteer_application', 'mentor_application'])) {
      $links[] = Link::createFromRoute($this->t('Community'), 'entity.node.canonical', array('node' => 15));
    }

    if (in_array($id, ['sponsorship_request'])) {
      $links[] = Link::createFromRoute($this->t('Sponsors'), 'entity.node.canonical', array('node' => 19));
    }

    $links[] = Link::createFromRoute($label, 'entity.contact_form.canonical', array('contact_form' => $id));

    $breadcrumb->setLinks($links);

    $breadcrumb->addCacheContexts(['url']);

    return $breadcrumb;
  }
}
