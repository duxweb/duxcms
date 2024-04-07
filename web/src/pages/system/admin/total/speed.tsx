import { Modal, useClient, useModal } from '@duxweb/dux-refine'
import { useTranslate } from '@refinedev/core'
import { Button, Loading, Checkbox, Input, Link } from 'tdesign-react/esm'
import { useEffect, useState } from 'react'

const Page = (props: Record<string, any>) => {
  const t = useTranslate()
  const { request } = useClient()
  const { onClose } = useModal()

  const [data, setData] = useState<any[]>([])
  const [loading, setLoading] = useState<boolean>(true)
  const [value, setValue] = useState<any[]>([])
  const [info, setInfo] = useState<Record<string, any>>()
  const [token, setToken] = useState<string>('')

  useEffect(() => {
    setLoading(true)
    request(`system/total/speed`, 'get')
      .then((res) => {
        setData(res.data || [])
        setValue(res.data || [])
        setInfo(res.meta || [])
      })
      .finally(() => {
        setLoading(false)
      })
  }, [])

  return (
    <div>
      <div className='flex flex-col gap-4 p-4'>
        <div>
          <div className='mb-2'>{t('system.total.speed.domain')}</div>
          <div>
            <Input disabled value={window.location.hostname} />
          </div>
        </div>
        <div>
          <div className='mb-2'>{t('system.total.speed.line')}</div>
          {loading ? (
            <Loading loading={true} text={t('system.total.speed.loading')} size='small'></Loading>
          ) : (
            <Checkbox.Group value={value} onChange={(v) => setValue(v)}>
              {data?.map((v, k) => (
                <Checkbox key={k} value={v}>
                  {v}
                </Checkbox>
              ))}
            </Checkbox.Group>
          )}
        </div>
        {info && !info?.token && (
          <div>
            <div className='mb-2'>{t('system.total.speed.token')}</div>
            <div>
              <Input value={token} onChange={(v) => setToken(v)} />
            </div>
            <div className='mt-2 text-placeholder'>
              {t('system.total.speed.tokenDesc')}{' '}
              <Link theme='primary' href='https://www.cesu.net/user/index' target='_blank'>
                {t('system.total.speed.tokenUrl')}
              </Link>
            </div>
          </div>
        )}
      </div>

      <Modal.Footer>
        <Button type='button' variant='outline' onClick={onClose}>
          {t('buttons.cancel')}
        </Button>
        <Button
          type='button'
          theme='primary'
          loading={loading}
          onClick={() => {
            onClose?.()
            props?.onSpeedTest?.(value, token)
          }}
        >
          {t('buttons.submit')}
        </Button>
      </Modal.Footer>
    </div>
  )
}

export default Page
