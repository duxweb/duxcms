import { useOne, useTranslate } from '@refinedev/core'
import { Descriptions } from '@duxweb/dux-refine'

const Page = (props: Record<string, any>) => {
  const id = props?.id
  const translate = useTranslate()

  const { data, isLoading } = useOne({
    id: id,
  })

  const info = data?.data || {}

  return (
    <div className='p-4'>
      <Descriptions direction='vertical' loading={isLoading}>
        <Descriptions.Item label={translate('system.operate.fields.requestMethod')}>
          {info?.request_method}
        </Descriptions.Item>
        <Descriptions.Item label={translate('system.operate.fields.requestUrl')}>
          {info?.request_url}
        </Descriptions.Item>
        <Descriptions.Item label={translate('system.operate.fields.requestTime')}>
          {info?.request_time}
        </Descriptions.Item>
        <Descriptions.Item label={translate('system.operate.fields.requestParams')}>
          <div className='app-code'>
            {info?.request_params ? JSON.stringify(info?.request_params, null, '\t') : ''}
          </div>
        </Descriptions.Item>
      </Descriptions>
    </div>
  )
}

export default Page
