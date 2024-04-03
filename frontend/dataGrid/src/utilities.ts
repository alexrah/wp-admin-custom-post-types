import type {tCouncilor, tReorderArrayEvent, tCouncilorReducerAction} from "./types";
import logger from "@alexrah/logger";

function reorderArray (event:tReorderArrayEvent, originalArray:tCouncilor[]) {
  const movedItem = originalArray.find((item, index) => index === event.oldIndex);
  const remainingItems = originalArray.filter((item, index) => index !== event.oldIndex);

  const reorderedItems = [
    ...remainingItems.slice(0, event.newIndex),
    movedItem,
    ...remainingItems.slice(event.newIndex)
  ];

  return reorderedItems;
}

function removeFromArray (event:Pick<tReorderArrayEvent, 'oldIndex'>,originalArray:tCouncilor[]){

  return originalArray.filter((item, index) => index !== event.oldIndex);

}


export function councilorReducer(state:tCouncilor[],action:tCouncilorReducerAction):tCouncilor[] {

  const lg = new logger();

  switch (action.type){
    case "add":
      lg.i('add');
      return [...state,{
        nome: '',
        cognome: '',
      }];

    case "remove":
      lg.i('remove');
      return removeFromArray(action.payload,state);

    case "reorder":
      lg.i('reorder');
      return reorderArray(action.payload,state);

  }

}