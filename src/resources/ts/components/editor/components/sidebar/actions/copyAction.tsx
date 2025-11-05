import {EditorComponentData} from '../../../types'
import {stringifyFields} from '../../../../../functions/object'
import {IconCheck, IconCode} from "../../ui/Icons";
import {Tooltip} from "../../ui/Tooltip";
import {useEffect, useRef, useState} from "preact/compat";
import {translate} from "../../../../../shared/functions/i18n";
import {prevent} from "../../../../../shared/functions/functions";
import {copyToClipboard} from "../../../../../functions/browser";


type CopyActionProps = {
    data: EditorComponentData | EditorComponentData[]
}

export function CopyAction({data, ...props}: CopyActionProps) {
    const [success, setSuccess] = useState(false)
    const timer = useRef<number>(0)
    const handleCopy = async () => {
        try {
            await copyToClipboard(stringifyFields(data))
            setSuccess(true)
            timer.current = window.setTimeout(() => {
                setSuccess(false)
            }, 4000)
        } catch (e) {
            alert(e)
        }
    }

    useEffect(() => {
        clearTimeout(timer.current)
    }, [])

    return (
        <Tooltip
            content={
                success ? (
                    <>
                        {translate('core-cms.admin.editor.sidebar.action.copy.success')}
                        <br/>
                        {translate('core-cms.admin.editor.sidebar.action.copy.instructions')}
                    </>
                ) : Array.isArray(data) ? translate('core-cms.admin.editor.sidebar.action.copy.page') : translate('core-cms.admin.editor.sidebar.action.copy.component')
            }
            trigger='mouseenter'
        >
            <button
                className="button padding-0"
                data-type="transparent"
                onClick={prevent(handleCopy)} {...props}
            >
                {success ? <IconCheck/> : <IconCode/>}
            </button>
        </Tooltip>
    )
}
