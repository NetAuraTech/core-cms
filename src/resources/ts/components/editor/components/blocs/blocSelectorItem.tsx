import {EditorComponentDefinition} from "../../types";
import {prevent} from "../../../../shared/functions/functions";
import {CSSProperties} from "preact/compat";
import ImagePreview from "./ImagePreview";


export function BlocSelectorItem({
                                     definition,
                                     name,
                                     onClick,
                                 }: {
    name: string
    definition: EditorComponentDefinition
    onClick: () => void
}) {
    const title = definition.title

    return (
        <button
            className={'grid button padding-0'}
            data-type="transparent"
            onClick={prevent(onClick)}
            title={definition.title}
            style={{    gridTemplateRows: "1fr auto"} as CSSProperties}
        >
            <ImagePreview imageName={`${name}.svg`}/>
            <h3 className="heading-3">{title}</h3>
        </button>
    )
}
