import React from 'react';
import styled from '@emotion/styled';
import type { tReducerAction,tMetaField } from "./types";

type tRowForm = {
  id: number,
  data: any|null,
  dispatchMetaDataRow: React.Dispatch<tReducerAction>,
  fieldName: string,
  metaFields: tMetaField[]

}

export default function RowForm({id,data,dispatchMetaDataRow,fieldName,metaFields}:tRowForm){

  const handleClickRemove = (e) => {
    e.preventDefault();
    dispatchMetaDataRow({type: 'remove',payload: {oldIndex: id, newIndex: null}})
  }

  const FormWrapper = styled.div`
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
    .reorderActions {
      display: flex;
      flex-direction: column;
      gap: 2px;
      button {
        font-size: 10px;
      }
    }
  `

  const FormElement = ({metaField}:{metaField:tMetaField}) => {

    switch (metaField.type){
      case 'text':
        return <input type="text" name={`${fieldName}[${id}][${metaField.name}]`} placeholder={metaField.label} defaultValue={data?data[metaField.name]:''}/>

      case 'checkbox':
        return (
          <label>
            {metaField.label}
            <input type="checkbox" name={`${fieldName}[${id}][${metaField.name}]`} defaultChecked={data?.[metaField.name]?true:false}/>
          </label>
        )
    }

  }

  return (
    <FormWrapper>
      <div className="reorderActions">
        <button onClick={() => dispatchMetaDataRow({type: 'reorder',payload: {oldIndex: id, newIndex: id-1}})}>⌃</button>
        <button onClick={() => dispatchMetaDataRow({type: 'reorder',payload: {oldIndex: id, newIndex: id+1}})}>⌄</button>
      </div>
      {metaFields.map(metaField => {
        return <FormElement key={metaField.name} metaField={metaField}/>
      })}
      <button onClick={handleClickRemove}>X</button>
    </FormWrapper>
  )

}