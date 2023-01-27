<?php

namespace Modules\Backend\Controllers;

use JasonGrimes\Paginator;
use MongoDB\BSON\ObjectId;

class Tags extends BaseController
{
    public function index()
    {
        $totalItems = $this->commonModel->count('tags',[]);
        $itemsPerPage = 20;
        $currentPage = $this->request->uri->getSegment(4, 1);
        $urlPattern = '/backend/pages/(:num)';
        $paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);
        $paginator->setMaxPagesToShow(5);
        $this->defData['paginator'] = $paginator;
        $bpk = ($this->request->uri->getSegment(4, 1) - 1) * $itemsPerPage;
        $this->defData['tags']=$this->commonModel->getList('tags',[],['limit'=>$itemsPerPage,'skip'=>$bpk]);
        return view('Modules\Backend\Views\tags\list',$this->defData);
    }

    public function create()
    {
        $valData = (['title' => ['label' => 'Etiket Başlığı', 'rules' => 'required'], 'seflink' => ['label' => 'Etiket URL', 'rules' => 'required'],]);
        if ($this->validate($valData) == false) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        if ($this->commonModel->createOne('tags', ['tag'=>$this->request->getPost('title'),'seflink'=>$this->request->getPost('seflink')])) return redirect()->route('tags', [1])->with('message', '<b>' . $this->request->getPost('title') . '</b> adlı etiket Oluşturuldu.');
        else return redirect()->back()->withInput()->with('error', 'Etiket oluşturulamadı.');
    }

    public function edit(string $id)
    {
        $this->defData['infos']=$this->commonModel->getOne('tags',['_id'=>new ObjectId($id)]);
        return view('Modules\Backend\Views\tags\update',$this->defData);
    }

    public function update(string $id)
    {
        $valData = (['title' => ['label' => 'Etiket Başlığı', 'rules' => 'required'], 'seflink' => ['label' => 'Etiket URL', 'rules' => 'required'],]);
        if ($this->validate($valData) == false) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        if ($this->commonModel->updateOne('tags',['_id'=>new ObjectId($id)], ['tag'=>$this->request->getPost('title'),'seflink'=>$this->request->getPost('seflink')])) return redirect()->route('tags', [1])->with('message', '<b>' . $this->request->getPost('title') . '</b> adlı etiket güncellendi.');
        else return redirect()->back()->withInput()->with('error', 'Etiket güncellenemedi.');
    }

    public function delete(string $id)
    {
        if ($this->commonModel->deleteMany('kun_tags_pivot',[''=>new ObjectId($id)]) && $this->commonModel->deleteOne('tags',['_id'=>new ObjectId($id)])) return redirect()->route('tags', [1])->with('message', 'Etiket silindi.');
        else return redirect()->back()->withInput()->with('error', 'Etiket silinemedi.');
    }
}
