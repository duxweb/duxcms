import { useCallback, useState } from 'react'
import { useTranslate } from '@refinedev/core'
import { Button, MessagePlugin, Loading } from 'tdesign-react/esm'
import { Modal, useClient, useModal } from '@duxweb/dux-refine'
import dayjs from 'dayjs'

interface PageProps {
  data: Record<string, any>
}

const Page = ({ data }: PageProps) => {
  const translate = useTranslate()
  const client = useClient()
  const [loading, setLoading] = useState(false)
  const [log, setLog] = useState('')

  const { onClose } = useModal()

  const submit = useCallback(() => {
    setLoading(true)
    client
      .request('cloud/apps/update', 'post', {
        data: {
          name: data.name,
        },
      })
      .then((res) => {
        if (res.code !== 200) {
          MessagePlugin.error(res.message)
          return
        }
        setLog(res?.data?.content)
      })
      .finally(() => {
        setLoading(false)
      })
  }, [])

  return (
    <>
      {log ? (
        <div className='p-4'>
          <pre className='overflow-auto rounded-lg p-4 bg-component'>{log}</pre>
        </div>
      ) : (
        <>
          <div className='p-4'>
            <div className='mb-2'>{translate('cloud.apps.tips.update')}</div>
            <div className='mb-2'>
              当前版本：{dayjs(data.local_time * 1000).format('YYYY-MM-DD HH:mm:ss')}
            </div>
            <div>
              最新版本：
              <span className='text-error'>
                {dayjs(data.time * 1000).format('YYYY-MM-DD HH:mm:ss')}
              </span>
            </div>
          </div>
          <Modal.Footer>
            <Button variant='outline' onClick={onClose}>
              {translate('cloud.apps.action.close')}
            </Button>
            <Button
              onClick={() => {
                submit()
              }}
            >
              {translate('cloud.apps.action.update')}
            </Button>
          </Modal.Footer>
        </>
      )}

      <Loading
        loading={loading}
        fullscreen
        preventScrollThrough={true}
        text={translate('cloud.apps.tips.loading')}
      ></Loading>
    </>
  )
}

export default Page
