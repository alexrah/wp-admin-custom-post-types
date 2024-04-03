import React from 'react';
import styled from "@emotion/styled";
import CouncilorForm from "./CouncilorForm";
import {tCouncilor} from "./types";
import logger from "@alexrah/logger";

export default function App(){

  const lg = new logger();

  const [countNew,setCountNew] = React.useState(0);

  const metaValues:tCouncilor[] = Object.values(window.wpAdminCPT['comunali_listaeletto-candidati_comunali']);
  lg.i('metaValues',metaValues);

  const handleAddCouncilor = (e) => {
    e.preventDefault();
    setCountNew(countNew => countNew+1);
  }

  lg.i('metaValues',metaValues);
  lg.i('countNew',countNew);

  const AppContainer = styled.div`
    display: flex;
    flex-direction: column;
    gap: 10px;
  `

  return (
    <AppContainer>
      {metaValues.map((metaValue,index) => {
        return <CouncilorForm key={`stored-${index}`} id={index} data={metaValue}/>
      })}
      {[...Array(countNew).keys()].map(key => {
        return <CouncilorForm key={`new-${key}`} id={metaValues.length+key} data={null}/>
      } )}
      <button onClick={handleAddCouncilor}>Aggiungi Consigliere</button>
    </AppContainer>
  )

}