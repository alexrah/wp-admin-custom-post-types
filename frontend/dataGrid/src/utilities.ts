import type { tReorderArrayEvent, tReducerAction } from "./types";
import logger from "@alexrah/logger";

function reorderArray (event:tReorderArrayEvent, originalArray:Array<any>) {
  const movedItem = originalArray.find((item, index) => index === event.oldIndex);
  const remainingItems = originalArray.filter((item, index) => index !== event.oldIndex);

  const reorderedItems = [
    ...remainingItems.slice(0, event.newIndex),
    movedItem,
    ...remainingItems.slice(event.newIndex)
  ];

  return reorderedItems;
}

function removeFromArray (event:Pick<tReorderArrayEvent, 'oldIndex'>,originalArray:Array<any>){

  return originalArray.filter((item, index) => index !== event.oldIndex);

}


export function metaDataRowReducer(state:Array<any>, action:tReducerAction):Array<any> {

  const lg = new logger();

  switch (action.type){
    case "add":
      lg.i('add');
      return [...state,{}];

    case "remove":
      lg.i('remove');
      return removeFromArray(action.payload,state);

    case "reorder":
      lg.i('reorder');
      return reorderArray(action.payload,state);

  }

}