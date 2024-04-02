import React from 'react';
import {tFormData} from "./types";

type tTestFormProps = {
  id: number;
  data: tFormData|null
}

export default function TestForm({id,data}:tTestFormProps){

  return (
    <div className='data-grid-test'>
      <input type="text" name={`comunali_listaeletto-candidati_comunali[${id}][nome]`} placeholder="Nome" defaultValue={data?data.nome:''}/>
      <input type="text" name={`comunali_listaeletto-candidati_comunali[${id}][cognome]`} placeholder="Cognome" defaultValue={data?data.cognome:''}/>
    </div>
  )

}