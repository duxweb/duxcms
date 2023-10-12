import { useCustom } from '@refinedev/core'
import { FormModal } from '@duxweb/dux-refine'
import { MagicFormRender } from '@duxweb/dux-extend'

const Page = (props: Record<string, any>) => {
  const magic = props.magic

  const { data } = useCustom<Record<string, any>>({
    url: 'tools/magic/config',
    method: 'get',
    meta: {
      params: {
        magic: magic,
      },
    },
  })

  return (
    <FormModal
      queryParams={{
        magic: magic,
      }}
      id={props?.id}
    >
      {data?.data?.fields && <MagicFormRender fields={data?.data?.fields} />}
    </FormModal>
  )
}

export default Page
