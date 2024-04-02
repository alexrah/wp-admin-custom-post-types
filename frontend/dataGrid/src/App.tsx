import React from 'react';
import TestForm from "./TestForm";
import {tCouncilor} from "./types";
import logger from "@alexrah/logger";

export default function App(){

  const lg = new logger();

  const metaValues:tCouncilor[] = Object.values(window.wpAdminCPT['comunali_listaeletto-candidati_comunali']);
  lg.i('metaValues',metaValues);

  return (
    <>
      {metaValues.map((metaValue,index) => {
        return <TestForm key={index} id={index} data={metaValue}/>
      })}
      <TestForm id={metaValues.length} data={null}/>
    </>
  )

}