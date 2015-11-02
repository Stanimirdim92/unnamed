<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Application\Controller;

final class MenuController extends BaseController
{
    /**
     * Get the contents for the menu/submenu.
     *
     * @return \Zend\View\Model\ViewModel
     */
    protected function postAction()
    {
        $this->getView()->setTemplate("application/menu/post");

        $contents = $this->getTable("Admin\\Model\\ContentTable")->queryBuilder();
        $contents->select('m.menulink, m.parent, m.keywords, m.description', 'c.menu, c.text, c.id, c.title, c.preview')
                 ->from('Admin\Entity\Menu', 'm')
                 ->innerJoin(
                    'Admin\Entity\Content',
                    'c',
                    \Doctrine\ORM\Query\Expr\Join::WITH,
                    'c.menu = m.id'
                 )
                 ->where("m.menulink = :menulink AND c.type = 0 AND c.language = :language")
                 ->setParameter(":menulink", (string) $this->getParam("post"))
                 ->setParameter(":language", (int) $this->language());

        $contents = $contents->getQuery()->getResult();
        if ($contents) {
            $this->initMetaTags($contents[0]);
            $this->getView()->contents = $contents;
        }

        return $this->getView();
    }
}
