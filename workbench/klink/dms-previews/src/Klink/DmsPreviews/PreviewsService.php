<?php namespace Klink\DmsPreviews;

use Illuminate\Support\ServiceProvider;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\File;
use KlinkDMS\User;
use KlinkDMS\Group;
use KlinkDMS\GroupType;
use KlinkDMS\Capability;
use KlinkDMS\Import;
use KlinkDMS\Option;
use KlinkDMS\Institution;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use KlinkDMS\Exceptions\ForbiddenException;
use KlinkDMS\Exceptions\FileAlreadyExistsException;
use KlinkDMS\Exceptions\FileNamingException;
use Illuminate\Support\Collection;
use KlinkDMS\Http\Request;
use Carbon\Carbon;
use KlinkDMS\Exceptions\GroupAlreadyExistsException;

class PreviewsService {

	private $supported_extensions = array('doc','ppt','xls','docx','xlsx','pptx','odt','odp', 'txt', 'md', 'rtf', 'gdoc', 'gslides', 'gsheet');

	/**
	 * [$adapter description]
	 * @var \Klink\DmsAdapter\KlinkAdapter
	 */
	private $adapter = null;

	/**
	 * Create a new DocumentsService instance.
	 *
	 * @return void
	 */
	public function __construct(\Klink\DmsAdapter\KlinkAdapter $adapter)
	{
		$this->adapter = $adapter;
	}



	public function render(\KlinkDMS\File $file){
		
		$extension = self::extension_from_file($file);
		
		$text_extractor = app()->make('Klink\DmsDocuments\FileContentExtractor');
			
		if($extension==='docx' || $extension==='rtf'){
		
			try{
		
				$phpWord = \PhpOffice\PhpWord\IOFactory::load($file->path);
				
				$prev = new HtmlPreviewWriter($phpWord);
				
				return $prev->getContent();
			}catch(\InvalidArgumentException $ex){
				
				\Log::error('Error while generating docx preview', ['file' => $file, 'error' => $ex]);
				
				return false;
			}
			catch(\Exception $ex){
				
				\Log::error('Error while generating docx preview', ['file' => $file, 'error' => $ex]);
				
				return false;
			}
		}
		else if($extension==='txt'){
		
			try{
				$content = $text_extractor->extract($file->mime_type, $file->path);
				 
				$content = str_replace("\n", '<br/>', $content);
				
      			return $content;
				
			}catch(\InvalidArgumentException $ex){
				
				\Log::error('Error while generating TXT preview', ['file' => $file, 'error' => $ex]);
				
				return false;
			}
			catch(\Exception $ex){
				
				\Log::error('Error while generating TXT preview', ['file' => $file, 'error' => $ex]);
				
				return false;
			}
		}
		else if($extension==='md'){
		
			try{
				$content = $text_extractor->extract($file->mime_type, $file->path);
				 
				$content = \Markdown::convertToHtml($content);
				
      			return $content;
				
			}catch(\InvalidArgumentException $ex){
				
				\Log::error('Error while generating MD preview', ['file' => $file, 'error' => $ex]);
				
				return false;
			}
			catch(\Exception $ex){
				
				\Log::error('Error while generating MD preview', ['file' => $file, 'error' => $ex]);
				
				return false;
			}
		}
		else if($extension==='gdoc' || $extension==='gslides' || $extension==='gsheet'){
		
			try{
				$content = $text_extractor->openAsText($file->path);
		
				$decoded = json_decode($content);
				
				if($decoded !== false){
					return $decoded->url;
				}
				
      			return null;
				
			}catch(\InvalidArgumentException $ex){
				
				\Log::error('Error while generating Google File preview', ['file' => $file, 'error' => $ex]);
				
				return false;
			}
			catch(\Exception $ex){
				
				\Log::error('Error while generating Google File preview', ['file' => $file, 'error' => $ex]);
				
				return false;
			}
		}
		else {
			return false;
		}
	}




	
	/**
	 * Return the extension of the file
	 */
	public static function extension_from_file(\KlinkDMS\File $file) {
	    
		try{

		    return \KlinkDocumentUtils::getExtensionFromMimeType($file->mime_type);

		}catch(\Exception $ex){
			return '';
		}
	}

}
