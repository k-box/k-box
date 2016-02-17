<?php namespace KlinkDMS;



final class RoutingHelpers {

	private static $group_route_cache = null;
	
	public static function thumbnail(DocumentDescriptor $doc){
		
		if($doc->isMine()){
			return route('klink_api', ['id' => $doc->local_document_id, 'action' => 'thumbnail']);	
		}
		
		return $doc->thumbnail_uri;
		
	}
	
	public static function document(DocumentDescriptor $doc){
		
		if($doc->isMine()){
			return route('klink_api', ['id' => $doc->local_document_id, 'action' => 'document']);	
		}
		
		return $doc->document_uri;
		
	}
	
	
	public static function preview(DocumentDescriptor $doc){
		
		if($doc->isMine()){
			return route('klink_api', ['id' => $doc->local_document_id, 'action' => 'document']) . '?preview=true';	
		}
		
		return $doc->document_uri;
		
	}
	
	public static function embed(DocumentDescriptor $doc){
		
		if($doc->isMine()){
			return route('klink_api', ['id' => $doc->local_document_id, 'action' => 'document']) . '?embed=true';	
		}
		
		return $doc->document_uri;
		
	}
	
	public static function download(DocumentDescriptor $doc){
		
		if($doc->isMine()){
			return route('klink_api', ['id' => $doc->local_document_id, 'action' => 'document']);	
		}
		
		return $doc->document_uri;
		
	}
	
	
	public static function group($id){
		
		if(is_null(self::$group_route_cache)){
			self::$group_route_cache = route('documents.groups.show', '');
		}
		
		return  self::$group_route_cache . '/' . $id;
	}
	
	
	public static function filterSearch($empty_url, $current_active_filters, $facet, $term, $selected = false){
	
		
		if(empty($current_active_filters)){
			return $empty_url . '&fs='.$facet.'&'.$facet.'='.$term;
		}
		else {
			
            $fs = array_keys($current_active_filters);
			
			$exists = in_array($facet, $fs); 
			 
			if(!$exists){
				$fs[] = $facet;
			}
            
            $active = array();
            
            foreach($current_active_filters as $key => $values){
                
				if($selected && $facet===$key && in_array($term, $values)){
					
				}
				else if(!$selected && $facet===$key && !in_array($term, $values)){
					$values[] = $term;
					$active[] = $key.'='.implode(',', $values);
				}
				else{
					$active[] = $key.'='.implode(',', $values);
				}
				
				//if($facet===$key && !$selected){
                	
				//}
                
            }
			
			if(!$exists && !$selected){
				$active[] = $facet.'='.$term;
			}
            
			// var_dump(compact('empty_url', 'current_active_filters', 'facet', 'term', 'selected', 'active'));
			
            return $empty_url . '&fs=' . implode(',', $fs).'&' . implode('&', $active);
        }
		
		
		
		
		// if($selected){
		// 	//not apply the current filter, but remove it
		// }
		// else {
		// 	return $full_url . '&fs='.$facet.'&'.$facet.'='.$term;
		// }
		// 
		// return $empty_url;
	}
	
	
	

}