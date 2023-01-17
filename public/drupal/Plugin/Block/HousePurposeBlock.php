<?php

namespace Drupal\residence\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Database\Database;

/**
 * Provides a block called "House Purpose block".
 *
 * @Block(
 *  id = "house_purpose_block",
 *  admin_label = @Translation("House Purpose block")
 * )
 */

class HousePurposeBlock extends BlockBase
{

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        \Drupal::service('page_cache_kill_switch')->trigger();
        $con = Database::getConnection();
        $languageCode = \Drupal::languageManager()->getCurrentLanguage()->getId();

        $query = $con->select('house_purposes', 'h')
            ->condition('active', 1)
            ->condition('language', $languageCode)
            ->fields('h');
        $house_purposes = $query->execute()->fetchAll();

        if(!$house_purposes){
            $default_language = UtilityController::getDefaultLanguage();
            $query = $con->select('house_purposes', 'h')
                ->condition('active', 1)
                ->condition('language', $default_language)
                ->fields('h');
            $house_purposes = $query->execute()->fetchAll();
        }
        
        return [
            '#theme' => 'house_purposes_list',
            '#house_purposes' => $house_purposes,
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
