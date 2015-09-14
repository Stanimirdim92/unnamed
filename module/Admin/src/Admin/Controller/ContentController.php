<?php
/**
 * MIT License
 * ===========
 *
 * Copyright (c) 2015 Stanimir Dimitrov <stanimirdim92@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.12
 * @link       TBA
 */

namespace Admin\Controller;

use Admin\Model\Content;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;
use Admin\Form\ContentForm;
use Zend\File\Transfer\Adapter\Http;
use Zend\Validator\File\IsImage;
use Zend\Validator\File\Size;
use Zend\Validator\File\Extension;

final class ContentController extends IndexController
{
    /**
     * @var ContentForm $contentForm
     */
    private $contentForm = null;

    /**
     * @param ContentForm $contentForm
     */
    public function __construct(ContentForm $contentForm = null)
    {
        parent::__construct();

        $this->contentForm = $contentForm;
    }

    /**
     * Initialize any variables before controller actions
     *
     * @param MvcEvent $e
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        parent::onDispatch($e);
        $this->addBreadcrumb(["reference"=>"/admin/content", "name"=>$this->translate("CONTENTS")]);
    }

    /**
     * This action shows the list of all contents
     */
    public function indexAction()
    {
        echo phpinfo();
        exit;
        $this->getView()->setTemplate("admin/content/index");
        if ((int) $this->getParam("id", 0) === 1) {
            $this->getView()->contents = $this->getTable("content")->fetchList(false, [], "type='1' AND content.language='".$this->language()."'", null, null,  "content.date DESC");
        } else {
            $this->getView()->contents = $this->getTable("content")->fetchJoin(false, "menu", [], [], "content.menu=menu.id", "inner", "type='0' AND content.language='".$this->language()."'", null, "menu.parent ASC, menu.menuOrder ASC, content.date DESC");
        }
        return $this->getView();
    }

    /**
     * This action serves for adding a new object of type Content
     */
    protected function addAction()
    {
        $this->getView()->setTemplate("admin/content/add");
        $this->initForm($this->translate("ADD_NEW_CONTENT"), null);
        $this->addBreadcrumb(["reference"=>"/admin/content/add", "name"=>$this->translate("ADD_NEW_CONTENT")]);
        return $this->getView();
    }

    /**
     * This action presents a modify form for Content object with a given id and session language
     * Upon POST the form is processed and saved
     */
    protected function modifyAction()
    {
        $this->getView()->setTemplate("admin/content/modify");
        $content = $this->getTable("content")->getContent((int)$this->getParam("id", 0), $this->language())->current();
        $this->getView()->content = $content;
        $this->addBreadcrumb(["reference"=>"/admin/content/modify/{$content->getId()}", "name"=> $this->translate("MODIFY_CONTENT")." &laquo;".$content->getTitle()."&raquo;"]);
        $this->initForm($this->translate("MODIFY_CONTENT"), $content);
        return $this->getView();
    }

    /**
     * this action deletes a content
     */
    protected function deleteAction()
    {
        $content = $this->getTable("content")->deleteContent((int)$this->getParam("id", 0), $this->language());
        $this->setLayoutMessages($this->translate("DELETE_CONTENT_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin/default', ['controller' => 'content']);
    }

    /**
     * this action shows content details
     */
    protected function detailAction()
    {
        $this->getView()->setTemplate("admin/content/detail");
        $content = $this->getTable("content")->getContent((int)$this->getParam("id", 0), $this->language())->current();
        $this->getView()->content = $content;
        $this->addBreadcrumb(["reference"=>"/admin/content/detail/".$content->getId()."", "name"=>"&laquo;". $content->getTitle()."&raquo; ".$this->translate("DETAILS")]);
        return $this->getView();
    }

    protected function deactivateAction()
    {
        $content = $this->getTable("content")->toggleActiveContent((int)$this->getParam("id", 0), 0);
        $this->setLayoutMessages($this->translate("CONTENT_DISABLE_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin/default', ['controller' => 'content']);
    }

    protected function activateAction()
    {
        $content = $this->getTable("content")->toggleActiveContent((int)$this->getParam("id", 0), 1);
        $this->setLayoutMessages($this->translate("CONTENT_ENABLE_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin/default', ['controller' => 'content']);
    }

    /**
     * This action will clone the object
     */
    protected function cloneAction()
    {
        $content = $this->getTable("content")->duplicate((int)$this->getParam("id", 0), $this->language())->current();
        $this->setLayoutMessages("&laquo;".$content->getTitle()."&raquo; ".$this->translate("CLONE_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin/default', ['controller' => 'content']);
    }

    /**
     * This is common function used by add and edit actions
     *
     * @param string $label button title
     * @param  Content|null $content
     */
    private function initForm($label = '', Content $content = null)
    {
        if (!$content instanceof Content) {
            $content = new Content([]);
        }

        /**
         * @var $form ContentForm
         */
        $form = $this->contentForm;
        $form->bind($content);
        $form->get("submit")->setValue($label);
        $this->getView()->form = $form;

        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($request->isXmlHttpRequest()) {
                return $this->prepareImages();
            } else {
                return $this->form($form, $content);
            }
        }
    }

    protected function deleteimageAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost()->toArray();

            if ($request->isXmlHttpRequest() && is_file("public".$data["img"])) {
                unlink("public".$data["img"]);
                return true;
            }
        }
        return false;
    }

    public function uploadProgressAction()
    {
        $id = $this->params()->fromQuery('id', null);
        $progress = new \Zend\ProgressBar\Upload\SessionProgress();
        return new \Zend\View\Model\JsonModel($progress->getProgress($id));
    }

    /**
     * @param ContentForm $form
     * @param Content $content
     */
    private function form(ContentForm $form, Content $content)
    {
        $request = $this->getRequest();
        $form->setInputFilter($form->getInputFilter());
        $data = array_merge_recursive(
            $request->getPost()->toArray(),
            $request->getFiles()->toArray()
        );

        $form->setData($data);
        if ($form->isValid()) {
            $formData = $form->getData();
            $userData = $this->UserData();

            if ($userData->checkIdentity(false, $this->translate("ERROR_AUTHORIZATION"))) {
                $name = $userData->getIdentity()->name." ".$userData->getIdentity()->surname;
            } else {
                $name = "Admin";
            }

            /**
             * We only need the name. All images ar stored in the same folder, based on the month and year
             */
            $formData->setPreview($formData->getPreview()["name"]);
            $formData->setAuthor($name);
            $this->getTable("content")->saveContent($content);
            $this->setLayoutMessages("&laquo;".$content->getTitle()."&raquo; ".$this->translate("SAVE_SUCCESS"), "success");
            return $this->redirect()->toRoute('admin/default', ['controller' => 'content']);
        } else {
            return $this->setLayoutMessages($form->getMessages(), "error");
        }
    }

    /**
     * Get all files from all folders and list them in the gallery
     */
    protected function filesAction()
    {
        chdir(getcwd()."/public/");
        if (!is_dir('userfiles/'.date("Y_M").'/images/')) {
            mkdir('userfiles/'.date("Y_M").'/images/', 0750, true);
        }
        $this->getView()->setTerminal(true);
        $dir = new \RecursiveDirectoryIterator('userfiles/', \FilesystemIterator::SKIP_DOTS);
        $it  = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::SELF_FIRST);
        $it->setMaxDepth(50);
        $files = [];
        $i = 0;
        foreach ($it as $file) {
            if ($file->isFile()) {
                $files[$i]["filelink"] = DIRECTORY_SEPARATOR.$file->getPath().DIRECTORY_SEPARATOR.$file->getFilename();
                $files[$i]["filename"] = $file->getFilename();
                $i++;
            }
        }
        chdir(dirname(getcwd()));
        $model = new JsonModel();
        $model->setVariables(["files" => $files]);
        return $model;
    }

    /**
     * Upload all images async
     */
    private function prepareImages()
    {
        $this->getView()->setTerminal(true);
        $adapter = new Http();
        /**
         * If validators are in the form, the adapter error messages won't be showed to the client
         */
        $size = new Size(['min'=>'10kB', 'max'=>'5MB','useByteString' => true]);
        $extension = new Extension(['jpg', 'gif','png','jpeg','bmp','webp','svg'], true);

        $adapter->setValidators([$size, new IsImage(), $extension]);

        if (!is_dir('public/userfiles/'.date("Y_M").'/images/')) {
            mkdir('public/userfiles/'.date("Y_M").'/images/', 0750, true);
        }

        $adapter->setDestination('public/userfiles/'.date("Y_M").'/images/');
        return $this->upload($adapter);
    }

    /**
     * @param  Http $adapter
     * @return Json
     */
    private function upload(Http $adapter)
    {
        $this->getView()->setTerminal(true);
        $uploadStatus = [];

        foreach ($adapter->getFileInfo() as $key => $file) {
            if ($key != "preview") {
                if ($adapter->isValid($file["name"])) {
                    $adapter->receive($file["name"]);
                    if ($adapter->isReceived($file["name"]) && $adapter->isUploaded($file["name"])) {


                        $this->createImageThumbnail($file["name"], 'public/userfiles/'.date("Y_M").'/images', 320, 320);


                        $uploadStatus["successFiles"][] = $file["name"]." ".$this->translate("UPLOAD_SUCCESS");
                    } else {
                        $uploadStatus["errorFiles"][] = $file["name"]." ".$this->translate("UPLOAD_FAIL");
                    }
                } else {
                    foreach ($adapter->getMessages() as $key => $msg) {
                        $uploadStatus["errorFiles"][] = $file["name"]." ".strtolower($msg);
                    }
                }
            }
        }
        // JsonModel doesn't work... It returns the page html even file upload s successful
        echo Json::encode($uploadStatus);
        die();
    }

    private function createImageThumbnail($imageName = null, $thumbnailPath = null, $thumbnailWidth = 0, $thumbnailHeight = 0)
    {
        $imageFile = $thumbnailPath."/".$imageName;
        $format = getimagesize($imageFile, $info);
        echo \Zend\Debug\Debug::dump(gd_info(), null, false);
        echo \Zend\Debug\Debug::dump(imagetypes(), null, false);
        $imageSize = getimagesize($imageFile);
        echo \Zend\Debug\Debug::dump(strtoupper(substr($imageSize["mime"], 6)), null, false);
        $imageType = $imageSize[2];

        switch ($imageType) {
            case 1:
                $image = "imagegif";
                $imageCreate = "imagecreatefromgif";
            break;

            case 2:
                $image = "imagejpeg";
                $imageCreate = "imagecreatefromjpeg";
            break;

            case 3:
                $image = "imagepng";
                $imageCreate = "imagecreatefrompng";
            break;

            default:
                $this->setLayoutMessages("ERROR", "error");
            break;
        }

        $oldImage = $imageCreate($imageFile);

        $newImage = imagecreatetruecolor($thumbnailWidth, $thumbnailHeight);
        imagecopyresampled($newImage, $oldImage, 0, 0, 0, 0, $thumbnailWidth, $thumbnailHeight, $imageSize[0], $imageSize[1]);

        $image($newImage, $imageFile);
        imagedestroy($newImage);

        return is_file($imageFile);
    }
}
