import React from 'react';
import styled from "@emotion/styled";
import logger from "@alexrah/logger";
import CouncilorForm from "./CouncilorForm";
import {councilorReducer} from './utilities';
import type {tCouncilor} from "./types";


export default function App(){

  const lg = new logger();

  const metaValues:tCouncilor[] = Object.values(window.wpAdminCPT['comunali_listaeletto-candidati_comunali']);
  lg.i('metaValues',metaValues);

  const [councilors,dispatchCouncilors] = React.useReducer(councilorReducer,metaValues);

  const handleAddCouncilor = (e) => {
    e.preventDefault();
    dispatchCouncilors({type: "add",payload: {oldIndex: null, newIndex: null }})
  }

  lg.i('metaValues',metaValues);
  // lg.i('countNew',countNew);

  const AppContainer = styled.div`
    display: flex;
    flex-direction: column;
    gap: 10px;
  `

  return (
    <AppContainer>
      {councilors.map((councilor,index) => {
        return <CouncilorForm key={`stored-${index}`} id={index} data={councilor} dispatchCouncilors={dispatchCouncilors}/>
      })}
      <button onClick={handleAddCouncilor}>Aggiungi Consigliere</button>
    </AppContainer>
  )

}