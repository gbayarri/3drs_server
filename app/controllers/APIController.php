<?php
namespace App\Controllers;

class APIController extends Controller {

      public function home($request, $response, $args) {
            $output = ['Back-end for '.$this->global['longProjectName'].' web application'];
            return $response
                        /*->withHeader('Access-Control-Allow-Origin', '*')
                        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')*/
                        ->withJson($output, 200, JSON_PRETTY_PRINT);
      }

	public function getPDBList($request, $response, $args) {
            $output = $this->utils->getPDBList($args['id']);
            return $response->withJson($output, 200, JSON_PRETTY_PRINT);
      }	

      public function uploadPDB($request, $response, $args) {

            $input = $request->getParsedBody();
            list($status, $id, $message) = $this->projectsController->createNewProject($input['structures'], 0);

            $output = ['status' => $status, 'id' => $id, 'message' => $message];
            return $response->withJson($output, 200, JSON_PRETTY_PRINT);
      }

      public function uploadFile($request, $response, $args) {
            $files = $request->getUploadedFiles();
            list($status, $id, $message) = $this->projectsController->createNewProject($files, 1);

            $output = ['status' => $status, 'id' => $id, 'message' => $message];
            return $response->withJson($output, 200, JSON_PRETTY_PRINT);
      }

      public function uploadTrajectory($request, $response, $args) {
            $files = $request->getUploadedFiles();
            $input = $request->getParsedBody();

            list($status, $id, $data, $message) = $this->trajectoriesController->addTrajectory($input, $files);

            $output = ['status' => $status, 'id' => $id, 'data' => $data, 'message' => $message];
            return $response->withJson($output, 200, JSON_PRETTY_PRINT);
      }

      public function getProjectInfo($request, $response, $args) {
            
            $output = $this->dataController->retrieveProjectInfo($args['id']);
            if(!$output) {
                  $code = 404;
                  $errMsg = "Requested project not found;";
	            throw new \Exception($errMsg, $code);
            }
            return $response

                        /*->withHeader('Access-Control-Allow-Origin', 'https://mmb.irbbarcelona.org')
                        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                        ->withHeader('Access-Control-Allow-Methods', 'GET')*/

                        ->withJson($output, 200, JSON_PRETTY_PRINT);

      }

      public function getFile($request, $response, $args) {

            $f = $this->dataController->retrieveData($args['id']);
            $i = $this->dataController->retrieveFileInfo($args['id']);

            $contentype = $this->utils->getContentType($i->file_type);
            $response = $response->withHeader('Content-Type', $contentype)
                        ->withHeader('Content-Description', 'File Transfer')
                        ->withHeader('Content-Disposition', 'attachment; filename="'.$i->name.'"')
                        ->withHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')

                        /*->withHeader('Access-Control-Allow-Origin', 'https://mmb.irbbarcelona.org')
                        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                        ->withHeader('Access-Control-Allow-Methods', 'GET')*/

                        ->withHeader('Pragma', 'public');

            echo $f;

            return $response;
      }

      public function updateProject($request, $response, $args) {

            $input = $request->getParsedBody();

            list($status, $message) = $this->dataController->updateData($args['id'], $input);

            return $response->withJson(['status' => $status, 'message' => $message], 200, JSON_PRETTY_PRINT);

      }

      public function shareProject($request, $response, $args) {

            list($status, $project, $shortid, $message) = $this->projectsController->cloneProject($args['id'], 'share');

            return $response->withJson(['status' => $status, 'project' => $project, 'shortid' => $shortid, 'message' => $message], 200, JSON_PRETTY_PRINT);

      }

      public function forkProject($request, $response, $args) {

            list($status, $project, $shortid, $message) = $this->projectsController->cloneProject($args['id'], 'fork');

            return $response->withJson(['status' => $status, 'project' => $project, 'message' => $message], 200, JSON_PRETTY_PRINT);

      }

      public function newRepresentation($request, $response, $args) {

            $input = $request->getParsedBody();

            list($status, $representation, $message) = $this->reprController->createRepresentation($args['id'], $input);

            return $response->withJson(['status' => $status, 'representation' => $representation, 'message' => $message], 200, JSON_PRETTY_PRINT);

      }

      public function cloneRepresentation($request, $response, $args) {

            $input = $request->getParsedBody();

            list($status, $representation, $message) = $this->reprController->cloneRepresentation($args['id'], $input);

            return $response->withJson(['status' => $status, 'representation' => $representation, 'message' => $message], 200, JSON_PRETTY_PRINT);

      }

      public function updateRepresentation($request, $response, $args) {

            $input = $request->getParsedBody();

            list($status, $message) = $this->reprController->updateRepresentation($args['id'], $args['repr'], $input);

            return $response->withJson(['status' => $status, 'message' => $message], 200, JSON_PRETTY_PRINT);

      }

      public function deleteRepresentation($request, $response, $args) {

            list($status, $representation, $message) = $this->reprController->deleteRepresentation($args['id'], $args['repr']);

            return $response->withJson(['status' => $status, 'newCurrentRepresentation' => $representation,  'message' => $message], 200, JSON_PRETTY_PRINT);

      }

      public function updateTrajectory($request, $response, $args) {

            $input = $request->getParsedBody();

            list($status, $message) = $this->trajectoriesController->updateTrajectory($args['id'], $input);

            return $response->withJson(['status' => $status, 'message' => $message], 200, JSON_PRETTY_PRINT);

      }

      public function getProjectSettings($request, $response, $args) {

            $input = $request->getParsedBody();

            list($status, $message) = $this->dataController->retrieveProjectSettings($input);

            return $response->withJson(['status' => $status, 'gallery' => $message], 200, JSON_PRETTY_PRINT);

      }

      public function getPublicProjects($request, $response, $args) {

            list($status, $message) = $this->dataController->retrievePublicProjects($input);

            return $response->withJson(['status' => $status, 'projects' => $message], 200, JSON_PRETTY_PRINT);

      }

      public function getIdFromShort($request, $response, $args) {

            list($status, $project) = $this->shortURLController->getProject($args['id']);

            return $response->withJson(['status' => $status, 'project' => $project], 200, JSON_PRETTY_PRINT);

      }
}
