import { useInvalidate } from '@refinedev/core'
import { Descriptions, UploadFile, useModal } from '@duxweb/dux-refine'

const Page = ({ id }: Record<string, any>) => {
  const invalidate = useInvalidate()
  const { onClose } = useModal()
  return (
    <div className='p-4'>
      <Descriptions direction='vertical'>
        <Descriptions.Item>
          <UploadFile
            draggable
            theme='file'
            className='w-full'
            onSuccess={() => {
              invalidate({
                resource: 'tools.file',
                invalidates: ['list'],
              })
              onClose?.()
            }}
            hookProps={{
              formatRequest: (requestData) => {
                requestData.dir_id = id
                return requestData
              },
            }}
          />
        </Descriptions.Item>
      </Descriptions>
    </div>
  )
}

export default Page
