import React from 'react';
import styled from '@emotion/styled';
import {tCouncilor} from "./types";

type tTestFormProps = {
  id: number;
  data: tCouncilor|null
}

export default function CouncilorForm({id,data}:tTestFormProps){

  const [ isActive,setIsActive ] = React.useState(true)

  const handleClickRemove = (e) => {
    e.preventDefault();
    setIsActive(false);
  }


  const CouncilorFormWrapper = styled.div`
    display: flex;
    gap: 10px;
    & > input,label {
      min-width: 20px;
    }
    & > input {
      flex: 1 0 20%;
    }
    input.consigliere-voti {
      flex: 0 1 10%;
    }
    label {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 5px;
      flex: 0 1 5%;
    }
    button {
      width: 40px;
    }
  `

  return (
    <>
    {isActive &&
      <CouncilorFormWrapper>
        <input type="text" name={`comunali_listaeletto-candidati_comunali[${id}][nome]`} placeholder="Nome" defaultValue={data?data.nome:''}/>
        <input type="text" name={`comunali_listaeletto-candidati_comunali[${id}][cognome]`} placeholder="Cognome" defaultValue={data?data.cognome:''}/>
        <input type="text" className='consigliere-voti' name={`comunali_listaeletto-candidati_comunali[${id}][voti]`} placeholder="Voti" defaultValue={data?data.voti:''}/>
        <label>
          Eletto
          <input type="checkbox" name={`comunali_listaeletto-candidati_comunali[${id}][isEletto]`} defaultValue={0}/>
        </label>
        <button onClick={handleClickRemove}>X</button>
      </CouncilorFormWrapper>
    }
    </>

  )

}