<?php
   /*
   Plugin Name: Hangar  plugin
   Description: Hangar prueba
   Version: 0
   Author: Daniel Bogarin
   License: GPL2
   */

  function getJsonFromFile( ) {
    $pathFile = plugin_dir_path( __FILE__ ).'songs.json';
    $json = json_decode( file_get_contents( $pathFile ), true );
    return $json;	
  }


  function getSongByName($songnametoseach)
  {
    $songsList= getJsonFromFile();
    $result = array();
    foreach($songsList as $song)
    {
      foreach($song as $data)
      {
          if (strpos($data['songname'], $songnametoseach) !== false) 
           {
             array_push($result, $data);
           }
      }
    }
    return $result;
  }

  function getSongByAlbumName($songAlbumtoSeach)
  {
    $songsList= getJsonFromFile();
    $result = array();
    foreach($songsList as $song)
    {
      foreach($song as $data)
      {
          if (strpos($data['albumname'], $songAlbumtoSeach) !== false) 
           {
             array_push($result, $data);
           }
      }
    }
    return $result;
  }

  function getSongByArtistName($songArtistName)
  {
    $songsList= getJsonFromFile();
    $result = array();
    foreach($songsList as $song)
    {
      foreach($song as $data)
      {
          if (strpos($data['artistname'], $songArtistName) !== false) 
           {
             array_push($result, $data);
           }
      }
    }
    return $result;
  }
 

  function searchsong($request_data)
  {
    $results = array();
    $parameters = $request_data->get_params();


  if( isset( $parameters['songname'] ) )
  {
    $songName= $parameters['songname']; 
    $resultByName = getSongByName($songName);
    array_push($results, $resultByName);
  }
  if( isset( $parameters['artistname'] ))
  {
    $artistName= $parameters['artistname']; 
    $resultByArtistName = getSongByArtistName($artistName);
    array_push($results, $resultByArtistName);
  }
  if( isset( $parameters['albumname'] ))
  {

    $albumname = $parameters['albumname'];
    $resultByAlbumName = getSongByAlbumName($albumname);
    array_push($results, $resultByAlbumName);
  }


  if( !isset( $parameters['songname'] ) &&  !isset( $parameters['artistname'] ) && !isset( $parameters['albumname']   ))
  {
    $results=getJsonFromFile();
  }
  
  $response = new WP_REST_Response( $results );
  $response->header( 'Access-Control-Allow-Origin', apply_filters( 'giar_access_control_allow_origin','*' ) );
  return $response;

}
function updateSong($parameters)
{
  if(!isset($parameters['id']) || empty($parameters['id']) )
  {
    return array('Error'=>'El id es requerido' );

  }

  $songId= (int)$parameters['id'];
  $songsList= getJsonFromFile();
  $pathFile = plugin_dir_path( __FILE__ ).'songs.json';
  $results= array();
  $update_success =false;
  $counterAppearances = 0;

  foreach($songsList as $song)
  {
    foreach($song  as $data)
    {
         if($data['id'] == $songId )
         {

          $url = isset($parameters['url']) ? $parameters['url'] : $data['url'];
          $songName = isset($parameters['songname']) ? $parameters['songname'] : $data['songname'];
          $artistID = isset($parameters['artistid']) ? $parameters['artistid'] : $data['artistid'];
          $artistname = isset($parameters['artistname']) ? $parameters['artistname'] : $data['artistname'];
          $albumid = isset($parameters['albumid']) ? $parameters['albumid'] :  $data['albumid'];
          $albumname = isset($parameters['albumname']) ? $parameters['albumname'] : $data['albumname'];

          $data['url'] = $url;
          $data['songname'] = $songName;
          $data['artistid'] = $artistID;
          $data['artistname'] = $artistname;
          $data['albumid'] = $albumid;
          $data['albumname'] = $albumname;
          array_push($results, $data);
         }
         else
         {
          array_push($results, $data);
          $counterAppearances++;
         }
    }
  }
  $newListSong =  array( 'songs' =>  $results);
  $newListSong = json_encode($newListSong);


  if($counterAppearances == 0)
  {
    $update_success = array('Mesage'=> 'Esa canción con ese Id no se encuentra.');
  }
  else
  {
    if(!file_put_contents($pathFile, $newListSong));
    {
      $update_success = array('Mesage'=> 'canción actualizada');
    }
  }
      
  $response = new WP_REST_Response( $update_success );
  $response->header( 'Access-Control-Allow-Origin', apply_filters( 'giar_access_control_allow_origin', '*' ) );
  return $response;  
}



function deleteSong($parameters)
{
  if(!isset($parameters['id']) || empty($parameters['id']) )
  {
    return array('Error'=>'El id es requerido' );
    
  }
  $songId= (int)$parameters['id'];
  $songsList= getJsonFromFile();
  $pathFile = plugin_dir_path( __FILE__ ).'songs.json';
  $results= array();
  $update_success =false;
  $counterAppearances = 0;

  foreach($songsList as $song)
  {
    foreach($song  as $data)
    {
         if($data['id'] !== $songId )
         {
            array_push($results, $data);
         }
         else
         {
            $counterAppearances++;
         }
    }
  }
  $newListSong =  array( 'songs' =>  $results);
  $newListSong = json_encode($newListSong);


  if($counterAppearances == 0)
  {
    $update_success = array('Mesage'=> 'Esa canción con ese Id no se encuentra.');
  }
  else
  {
    if(!file_put_contents($pathFile, $newListSong));
    {
      $update_success = array('Mesage'=> 'canción borrada');
    }
  }
      
  $response = new WP_REST_Response( $update_success );
  $response->header( 'Access-Control-Allow-Origin', apply_filters( 'giar_access_control_allow_origin', '*' ) );
  return $response;  
}

  function createSong($parameters)
  {

    $update_success =array('Message'=>'Error al crear la canción.');
    $url = isset($parameters['url']) ? $parameters['url'] : "Not url";
    $songName = isset($parameters['songname']) ? $parameters['songname'] : "Los pollitos :( ";
    $artistID = isset($parameters['artistid']) ? $parameters['artistid'] : rand(100,10000);
    $artistname = isset($parameters['artistname']) ? $parameters['artistname'] : 'Chente';
    $albumid = isset($parameters['albumid']) ? $parameters['albumid'] : rand(100,10000);
    $albumname = isset($parameters['albumname']) ? $parameters['albumname'] : 'Grandes exitos';
    
    $newSong= array(
      'url'=> $url,
      'id' => rand(100,1000000000),
      'songname'=> $songName,
      'artistid'=> $artistID,
      'artistname'=>$artistname,
      'albumid'=> $albumid,
      'albumname'=> $albumname
    );

    $currentsongs= getJsonFromFile();
    $currentsongs =$currentsongs['songs'];
    array_push($currentsongs,$newSong);

    $pathFile = plugin_dir_path( __FILE__ ).'songs.json';
    $currentsongs= array('songs'=> $currentsongs);
    $currentsongs =  json_encode($currentsongs);
    
    if(!file_put_contents($pathFile, $currentsongs));
    {
       $update_success =array('Message'=>'Canción creada correctamente');
    }
    $response = new WP_REST_Response( $update_success );
    $response->header( 'Access-Control-Allow-Origin', apply_filters( 'giar_access_control_allow_origin', '*' ) );
    return $response;
  }


  add_action( 'rest_api_init', function () {


    register_rest_route( 'hangar-api/v1', '/song', array(
      'methods' => 'GET',
      'callback' => 'searchsong',
      'args'                => array(
        'songname' => array(
          'required'=> false,
          'type'        => 'string',
          'description' => __( 'Nombre de la canción.' ),
        ),
        'artistname' => array(
          'required'=> false,
          'type'        => 'string',
          'description' => __( 'Artista de la canción.' ),
        ),
        'albumname' => array(
          'required'=> false,
          'type'        => 'string',
          'description' => __( 'Album de la canción.' ),
        ),
      ),
    ) );


    register_rest_route( 'hangar-api/v1', '/song', array(
			'methods' => 'POST',
      'callback' =>  'createsong',
      'args'                => array(
        'url' => array(
          'required'=> false,
          'type'        => 'string',
          'description' => __( 'URL de la canción.' ),
        ),

        'songname' => array(
          'required'=> false,
          'type'        => 'string',
          'description' => __( 'Nombre de la canción.' ),
        ),

        'artistid' => array(
          'required'=> false,
          'type'        => 'Integer',
          'description' => __( 'Id del artista.' ),
        ),
        'artistname' => array(
          'required'=> false,
          'type'        => 'string',
          'description' => __( 'Nombre del Artisa.' ),
        ),
        'albumid' => array(
          'required'=> false,
          'type'        => 'Integer',
          'description' => __( 'Id del album.' ),
        ),
        'albumname' => array(
          'required'=> false,
          'type'        => 'string',
          'description' => __( 'Nombre del album.' ),
        ),
      ),
    ) );

    register_rest_route( 'hangar-api/v1', '/song', array(
			'methods' => 'DELETE',
      'callback' =>  'deleteSong',
      'args'                => array(
        'id' => array(
          'required'=> true,
          'type'        => 'Int',
          'description' => __( 'Id de la canción.' ),
        ),
      ),
    ) );
    
    register_rest_route( 'hangar-api/v1', '/song', array(
			'methods' => 'PUT',
      'callback' =>  'updateSong',
      'args'                => array(
        'id' => array(
          'required'=> true,
          'type'        => 'Int',
          'description' => __( 'Id de la canción.' ),
        ),
      ),
		) );

  } );

?>
