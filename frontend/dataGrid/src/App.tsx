import React from 'react';
import styled from "@emotion/styled";
import logger from "@alexrah/logger";
import RowForm from "./RowForm";
import {metaDataRowReducer} from './utilities';
import type { tMetaField } from "./types";

type tAppProps = {
  fieldName: string
}

export default function App({fieldName}:tAppProps){

  const lg = new logger();

  const metaValues:Array<any> = Object.values(window.wpAdminCPT[fieldName].data);
  lg.i('metaValues',metaValues);

  const metaFields:tMetaField[] = Object.values(window.wpAdminCPT[fieldName].fields);
  lg.i('metaFields',metaFields);

  const [metaDataRow,dispatchMetaDataRow] = React.useReducer(metaDataRowReducer,metaValues);

  const handleAddDataRow = (e) => {
    e.preventDefault();
    dispatchMetaDataRow({type: "add",payload: {oldIndex: null, newIndex: null }})
  }

  lg.i('metaValues',metaValues);

  const AppContainer = styled.div`
    display: flex;
    flex-direction: column;
    gap: 10px;
  `

  return (
    <AppContainer>
      {metaDataRow.map((metaData,index) => {
        return <RowForm fieldName={fieldName} metaFields={metaFields} key={`stored-${index}`} id={index} data={metaData} dispatchMetaDataRow={dispatchMetaDataRow}/>
      })}
      <button onClick={handleAddDataRow}>Aggiungi Riga</button>
    </AppContainer>
  )

}