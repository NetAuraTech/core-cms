import '@grafikart/drop-files-element'
import DatePickerElement from "./elements/DatePicker";
import {Editor} from "./elements/editor";


DatePickerElement.defineElement();

const editor = new Editor()
window.editor = editor