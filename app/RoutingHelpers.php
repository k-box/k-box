<?php

namespace KlinkDMS;

final class RoutingHelpers
{
    private static $group_route_cache = null;
    
    public static function thumbnail(DocumentDescriptor $doc)
    {
        if ($doc->isMine()) {
            return route('klink_api', ['id' => $doc->local_document_id, 'action' => 'thumbnail']);
        }
        
        return $doc->thumbnail_uri;
    }
    
    public static function document(DocumentDescriptor $doc)
    {
        if ($doc->isMine()) {
            return route('klink_api', ['id' => $doc->local_document_id, 'action' => 'document']);
        }
        
        return $doc->document_uri;
    }
    
    
    public static function preview(DocumentDescriptor $doc)
    {
        if ($doc->isMine()) {
            return route('klink_api', ['id' => $doc->local_document_id, 'action' => 'preview']);
        }
        
        return $doc->document_uri;
    }
    
    public static function embed(DocumentDescriptor $doc)
    {
        if ($doc->isMine()) {
            return route('klink_api', ['id' => $doc->local_document_id, 'action' => 'download']).'?embed=true';
        }
        
        return $doc->document_uri;
    }
    
    public static function download(DocumentDescriptor $doc)
    {
        if ($doc->isMine()) {
            return route('klink_api', ['id' => $doc->local_document_id, 'action' => 'download']);
        }
        
        return $doc->document_uri;
    }
    
    
    public static function group($id)
    {
        if (is_null(self::$group_route_cache)) {
            self::$group_route_cache = route('documents.groups.show', '');
        }
        
        return  self::$group_route_cache.'/'.$id;
    }
    
    
    public static function filterSearch($empty_url, $current_active_filters, $facet, $term, $selected = false)
    {
        $url_components = [];

        
        if (empty($current_active_filters)) {
            $url_components[] = $facet.'='.$term;
        } else {
            $fs = array_keys($current_active_filters);
            
            $exists = in_array($facet, $fs);
             
            if (! $exists) {
                $fs[] = $facet;
            }
            
            $active = [];

            foreach ($current_active_filters as $key => $values) {
                $values = array_unique($values);
                
                if ($selected && $facet===$key && in_array($term, $values)) {
                    $diff = array_diff($values, [$term]);
                    if (! empty($diff)) {
                        $active[] = $key.'='.implode(',', $diff);
                    }
                } elseif (! $selected && $facet===$key && ! in_array($term, $values)) {
                    $values[] = $term;
                    $active[] = $key.'='.implode(',', $values);
                } else {
                    $active[] = $key.'='.implode(',', $values);
                }
            }
            
            if (! $exists && ! $selected) {
                $active[] = $facet.'='.$term;
            }
            

            if (! empty($active)) {
                $url_components[] = implode('&', $active);
            }
        }

        $url_components = array_filter(array_merge([$empty_url], $url_components), function ($itm) {
            return ! empty($itm) && $itm!=='?';
        });

        $url_to_return = implode('&', $url_components);

        return (! starts_with($url_to_return, '?') ? '?' : '').$url_to_return;
    }
}
