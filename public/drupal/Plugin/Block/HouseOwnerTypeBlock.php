<?php

namespace Drupal\residence\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Database\Database;

/**
 * Provides a block called "House Owner Type block".
 *
 * @Block(
 *  id = "house_owner_type_block",
 *  admin_label = @Translation("House Owner Type block")
 * )
 */

class HouseOwnerTypeBlock extends BlockBase
{

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        \Drupal::service('page_cache_kill_switch')->trigger();
        $con = Database::getConnection();
        $languageCode = \Drupal::languageManager()->getCurrentLanguage()->getId();

        $query = $con->select('house_owner_types', 'h')
            ->condition('active', 1)
            ->condition('language', $languageCode)
            ->fields('h');
        $house_owner_types = $query->execute()->fetchAll();

        if(!$house_owner_types){
            $default_language = UtilityController::getDefaultLanguage();
            $query = $con->select('house_owner_types', 'h')
                ->condition('active', 1)
                ->condition('language', $default_language)
                ->fields('h');
            $house_owner_types = $query->execute()->fetchAll();
        }
        
        return [
            '#theme' => 'house_owner_types_list',
            '#house_owner_types' => $house_owner_types,
        ];
    }

    /**
     * @return int
     */
    public function getCacheMaxAge() 
    {
        return 1;
    }
}
