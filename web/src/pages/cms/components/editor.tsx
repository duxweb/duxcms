import { MarkdownEditor, Editor as Timymce } from '@duxweb/dux-refine'
import { useOne } from '@refinedev/core'

interface EditorProps {
  defaultValue?: string
  value?: string
  onChange?: (value: unknown) => void
}

export const Editor = ({ ...props }: EditorProps) => {
  const { data } = useOne({
    resource: 'cms.setting',
    queryOptions: {
      enabled: true,
    },
  })

  const editor = data?.data?.cms?.editor || 'default'

  return (
    <div className='w-full'>
      {editor == 'default' && <Timymce {...props} />}
      {editor == 'markdown' && <MarkdownEditor {...props} />}
    </div>
  )
}
