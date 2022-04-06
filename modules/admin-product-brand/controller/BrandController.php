<?php
/**
 * BrandController
 * @package admin-product-brand
 * @version 0.0.1
 */

namespace AdminProductBrand\Controller;

use LibFormatter\Library\Formatter;
use LibForm\Library\Form;
use LibForm\Library\Combiner;
use LibPagination\Library\Paginator;
use ProductBrand\Model\ProductBrand as PBrand;
use Product\Model\Product;

class BrandController extends \Admin\Controller
{
    private function getParams(string $title): array{
        return [
            '_meta' => [
                'title' => $title,
                'menus' => ['product', 'brand']
            ],
            'subtitle' => $title,
            'pages' => null
        ];
    }

    public function editAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_product_brand)
            return $this->show404();

        $brand = (object)[];

        $id = $this->req->param->id;
        if($id){
            $brand = PBrand::getOne(['id'=>$id]);
            if(!$brand)
                return $this->show404();
            $params = $this->getParams('Edit Product Brand');
        }else{
            $params = $this->getParams('Create New Product Brand');
        }

        $form           = new Form('admin.product-brand.edit');
        $params['form'] = $form;

        $c_opts = [
            'meta'   => [null, null, 'json']
        ];

        $combiner = new Combiner($id, $c_opts, 'product-brand');
        $brand = $combiner->prepare($brand);

        $params['opts'] = $combiner->getOptions();

        if(!($valid = $form->validate($brand)) || !$form->csrfTest('noob'))
            return $this->resp('product/brand/edit', $params);

        $valid = $combiner->finalize($valid);

        if($id){
            if(!PBrand::set((array)$valid, ['id'=>$id]))
                deb(PBrand::lastError());
        }else{
            $valid->user = $this->user->id;
            if(!PBrand::create((array)$valid))
                deb(PBrand::lastError());
        }

        // add the log
        $this->addLog([
            'user'   => $this->user->id,
            'object' => $id,
            'parent' => 0,
            'method' => $id ? 2 : 1,
            'type'   => 'product-brand',
            'original' => $brand,
            'changes'  => $valid
        ]);

        $next = $this->router->to('adminProductBrand');
        $this->res->redirect($next);
    }

    public function indexAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_product_brand)
            return $this->show404();

        $cond = $pcond = [];
        if($q = $this->req->getQuery('q'))
            $pcond['q'] = $cond['q'] = $q;

        list($page, $rpp) = $this->req->getPager(25, 50);

        $brands = PBrand::get($cond, $rpp, $page, ['name'=>true]) ?? [];
        if($brands)
            $brands = Formatter::formatMany('product-brand', $brands, ['user']);

        $params           = $this->getParams('Product Brand');
        $params['brands'] = $brands;
        $params['form']   = new Form('admin.product-brand.index');

        $params['form']->validate( (object)$this->req->get() );

        // pagination
        $params['total'] = $total = PBrand::count($cond);
        if($total > $rpp){
            $params['pages'] = new Paginator(
                $this->router->to('adminProductBrand'),
                $total,
                $page,
                $rpp,
                10,
                $pcond
            );
        }

        $this->resp('product/brand/index', $params);
    }

    public function removeAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_product_brand)
            return $this->show404();

        $id    = $this->req->param->id;
        $brand = PBrand::getOne(['id'=>$id]);
        $next  = $this->router->to('adminProductBrand');
        $form  = new Form('admin.product-brand.index');

        if(!$brand)
            return $this->show404();

        if(!$form->csrfTest('noob'))
            return $this->res->redirect($next);

        // add the log
        $this->addLog([
            'user'   => $this->user->id,
            'object' => $id,
            'parent' => 0,
            'method' => 3,
            'type'   => 'product-brand',
            'original' => $brand,
            'changes'  => null
        ]);

        PBrand::remove(['id'=>$id]);
        Product::set(['brand' => null], ['brand' => $id]);

        $this->res->redirect($next);
    }
}
