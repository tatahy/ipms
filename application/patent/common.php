<?php
#patent的period与status的对应关系
const conPatPeriodVsStatus=[
  'audit'=>['chi'=>'内审','status'=>['_PATS1','_PATS2-1','_PATS2-2','_PATS2-3','_PATS2-END2']],
  'newAdd'=>['chi'=>'拟申报','status'=>['_PATS2-END1','_PATS4-2']],
  'apply'=>['chi'=>'申报','status'=>['_PATS3-1','_PATS3-2','_PATS3-END1','_PATS4-1']],
  'authorize'=>['chi'=>'授权(有效)','status'=>['_PATS4-END1','_PATS4-END2']],
  'invalid'=>['chi'=>'无授权(无效)','status'=>['_PATS5','_PATS3-END2']]
];

