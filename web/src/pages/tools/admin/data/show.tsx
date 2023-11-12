import { useCustom, useShow } from '@refinedev/core'
import { Descriptions } from '@duxweb/dux-refine'
import { MagicShowRender } from '@duxweb/dux-extend'

const Page = (props: Record<string, any>) => {
  const { data: magic } = useCustom<Record<string, any>>({
    url: 'tools/magic/config',
    method: 'get',
    meta: {
      params: {
        magic: props.magic,
      },
    },
  })

  const { queryResult } = useShow({
    resource: 'tools.data',
    id: props?.id,
    meta: {
      params: {
        magic: props.magic,
        action: 'show',
      },
    },
  })

  const info = queryResult.data?.data

  return (
    <div className='p-4'>
      <Descriptions direction='vertical' loading={queryResult?.isLoading}>
        {magic?.data?.fields && <MagicShowRender fields={magic.data.fields} data={info} />}
      </Descriptions>
    </div>
  )
}

export default Page
