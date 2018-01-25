<?php

namespace KBox;

final class RoutingHelpers
{
    private static $group_route_cache = null;
    
    public static function thumbnail(DocumentDescriptor $doc, File $version = null)
    {
        if (! $doc->isMine()) {
            return $doc->thumbnail_uri;
        }

        $params = ['id' => $doc->local_document_id, 'action' => 'thumbnail'];

        if ($version) {
            $params['version'] = $version->uuid;
        }

        return route('klink_api', $params);
    }
    
    public static function document(DocumentDescriptor $doc, File $version = null)
    {
        if (! $doc->isMine()) {
            return $doc->document_uri;
        }
        
        $params = ['id' => $doc->local_document_id, 'action' => 'document', 'version' => null];

        if ($version) {
            $params['version'] = $version->uuid;
        }

        return route('klink_api', $params);
    }
    
    
    public static function preview(DocumentDescriptor $doc, File $version = null)
    {
        if (! $doc->isMine()) {
            return $doc->document_uri;
        }

        $params = ['id' => $doc->local_document_id, 'action' => 'preview'];

        if ($version) {
            $params['version'] = $version->uuid;
        }

        return route('klink_api', $params);
    }
    
    public static function embed(DocumentDescriptor $doc, File $version = null)
    {
        if ($doc->isMine()) {
            return self::download($doc, $version).'?embed=true';
        }
        
        return $doc->document_uri;
    }
    
    public static function download(DocumentDescriptor $doc, File $version = null)
    {
        if (! $doc->isMine()) {
            return $doc->document_uri;
        }

        $params = ['id' => $doc->local_document_id, 'action' => 'download'];

        if ($version) {
            $params['version'] = $version->uuid;
        }

        return route('klink_api', $params);
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
