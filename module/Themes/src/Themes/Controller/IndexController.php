<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Themes\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

final class IndexController extends AbstractActionController
{
    /**
     * @var array
     */
    private $themesConfig = [];

    /**
     * @var mixed
     */
    private $reloadService = null;

    /**
     * @method __construct
     *
     * @param array $themesConfig
     * @param mixed $reloadService
     */
    public function __construct(array $themesConfig = [], $reloadService)
    {
        $this->themesConfig = $themesConfig;
        $this->reloadService = $reloadService;
    }

    /**
     * This action shows the list of all themes.
     *
     * @method indexAction
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $filename = __DIR__.'/../../../config/module.config.php';
            $settings = include $filename;
            $themeName = $request->getPost()['themeName'];
            $settings['theme']['name'] = $themeName;
            file_put_contents($filename, '<?php return '.var_export($settings, true).';');
            $this->setLayoutMessages($this->translate("THEME_ACTIVATED"), 'success');
            $this->reloadService->reload();
        }

        return new ViewModel([
            'themes' => $this->themesConfig,
        ]);
    }
}
